<?php

namespace App\Http\Controllers\Api\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ListingController extends Controller
{
    public function changeStatusListing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference' => ['required', 'string', 'max:255'],
            'active'    => ['required', 'integer', 'in:0,1'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $reference = trim((string) $request->input('reference'));
        $active    = (int) $request->input('active');

        try {
            // Update by reference
            $updated = DB::table('listings')
                ->where('reference', $reference)
                ->update([
                    'active'     => $active,
                    'updated_at' => now(),
                ]);

            if ($updated === 0) {
                // Either not found OR same value already set.
                // Check existence to return correct message.
                $exists = DB::table('listings')->where('reference', $reference)->exists();

                if (!$exists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Listing not found for given reference',
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'No change (already same status)',
                    'data'    => [
                        'reference' => $reference,
                        'active'    => $active,
                    ],
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Listing status updated successfully',
                'data'    => [
                    'reference' => $reference,
                    'active'    => $active,
                ],
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error while updating listing status',
                // Optional: hide this in production
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function fetch_all_listings(Request $request)
{
    // ✅ Inputs
    $perPage = (int) $request->get('per_page', 10);
    $page    = (int) $request->get('page', 1);
    $search  = trim((string) $request->get('search', ''));

    // ✅ Clamp per_page (avoid idiots / heavy loads)
    if ($perPage < 1) $perPage = 10;
    if ($perPage > 100) $perPage = 100;

    // ✅ Base query: select only requested columns
    $query = DB::table('listings')->select([
        'id',
        'reference',
        'property_t',
        'price',
        'community',
        'sub_community',
        'property',
        'property_type',
        'property_category',
        'title',
        'active',
        'furnishing',
        'created_at',

        // ✅ First image (lowest sorting) from listing_images
        DB::raw("(
            SELECT li.image
            FROM listing_images li
            WHERE li.listing_id = listings.id
              AND li.active = 1
            ORDER BY (li.sorting IS NULL) ASC, li.sorting ASC, li.id ASC
            LIMIT 1
        ) AS first_image"),
    ]);

    // ✅ Search by reference (partial match)
    if ($search !== '') {
        $query->where('reference', 'LIKE', '%' . $search . '%');
    }

    // ✅ Sort newest first (you can change)
    $query->orderByDesc('id');

    // ✅ Pagination
    $paginator = $query->paginate($perPage, ['*'], 'page', $page);

    return response()->json([
        'success' => true,
        'message' => 'Listings fetched successfully',
        'data' => $paginator->items(),
        'pagination' => [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
        ],
    ], 200);
}

 public function showListingsOptions(Request $request)
{
    $searchText = trim((string) $request->query('search_text', ''));

    if ($searchText === '') {
        return response()->json([
            'success' => true,
            'data' => [],
        ], 200);
    }

    $like = '%' . $searchText . '%';

    // 1) Communities
    $communities = DB::table('listings')
        ->select('community_id as id', 'community as name', 'community_slug as slug')
        ->whereNotNull('community_id')
        ->whereNotNull('community')
        ->whereNotNull('community_slug')
        ->where('community', 'LIKE', $like)
        ->groupBy('community_id', 'community', 'community_slug')
        ->orderBy('community')
        ->limit(20)
        ->get()
        ->map(fn ($r) => [
            'id'   => (int) $r->id,
            'name' => $r->name,
            'slug' => $r->slug,
            'type' => 'community',
        ]);

    // 2) Sub-communities
    $subCommunities = DB::table('listings')
        ->select('sub_community_id as id', 'sub_community as name', 'sub_community_slug as slug')
        ->whereNotNull('sub_community_id')
        ->whereNotNull('sub_community')
        ->whereNotNull('sub_community_slug')
        ->where('sub_community', 'LIKE', $like)
        ->groupBy('sub_community_id', 'sub_community', 'sub_community_slug')
        ->orderBy('sub_community')
        ->limit(20)
        ->get()
        ->map(fn ($r) => [
            'id'   => (int) $r->id,
            'name' => $r->name,
            'slug' => $r->slug,
            'type' => 'sub_community',
        ]);

    // 3) Properties
    $properties = DB::table('listings')
        ->select('property_id as id', 'property as name', 'property_slug as slug')
        ->whereNotNull('property_id')
        ->whereNotNull('property')
        ->whereNotNull('property_slug')
        ->where('property', 'LIKE', $like)
        ->groupBy('property_id', 'property', 'property_slug')
        ->orderBy('property')
        ->limit(20)
        ->get()
        ->map(fn ($r) => [
            'id'   => (int) $r->id,
            'name' => $r->name,
            'slug' => $r->slug,
            'type' => 'property',
        ]);

    $data = $communities
        ->concat($subCommunities)
        ->concat($properties)
        ->values();

    return response()->json([
        'success' => true,
        'data' => $data,
    ], 200);
}

public function resolveSearchSlugs(Request $request)
    {
        $slugs = $request->query('search', []);

        if (!is_array($slugs) || empty($slugs)) {
            return response()->json([
                "status" => true,
                "data" => []
            ]);
        }

        // sanitize + unique
        $slugs = collect($slugs)
            ->filter(fn($s) => is_string($s) && trim($s) !== "")
            ->map(fn($s) => trim($s))
            ->unique()
            ->values()
            ->all();

        // ---- Lookup in multiple tables ----
        $communities = DB::table("communities")
            ->selectRaw("id, name, slug, 'community' as type")
            ->whereIn("slug", $slugs)
            ->get();

        $subCommunities = DB::table("sub_communities")
            ->selectRaw("id, name, slug, 'sub_community' as type")
            ->whereIn("slug", $slugs)
            ->get();

        $properties = DB::table("properties")
            ->selectRaw("id, name, slug, 'property' as type")
            ->whereIn("slug", $slugs)
            ->get();

        $all = $communities
            ->merge($subCommunities)
            ->merge($properties);

        // ---- Keep same order as input slugs ----
        $map = $all->keyBy("slug");

        $ordered = collect($slugs)->map(function ($slug) use ($map) {
            if ($map->has($slug)) return $map->get($slug);

            // fallback object (so nothing breaks if slug not found)
            return (object) [
                "id" => $slug,
                "name" => str_replace("-", " ", $slug),
                "slug" => $slug,
                "type" => "property",
            ];
        })->values();

        return response()->json([
            "status" => true,
            "data" => $ordered
        ]);
    }


public function listingsBySlug(Request $request)
    {
        $community_name = trim((string) $request->query('community_name', ''));
        // $community_name = trim((string) $request->input('community_name', ''));

        if ($community_name === '') {
            return response()->json([
                'success' => false,
                'message' => 'Community is required.',
                'data' => null,
            ], 422);
        }

        // ✅ select columns that exist in your listings table
        $listingSelect = [
            'id',
            'reference',
            'unit_id',
            'property_t',
            'price',
            'bedrooms',
            'bathrooms',
            'area',
            'community',
            'community_slug',
            'sub_community',
            'sub_community_slug',
            'property',
            'property_slug',
            'property_type',
            'property_category', // Sale / Rent
            'is_featured',
            'active',
            'title',
            'offplan',
        ];

        $base = DB::table('listings')
            ->select($listingSelect)
            ->where('community', $community_name)
            ->where('active', 1);

        // ✅ latest = highest id
        $sale = (clone $base)
            ->where('property_category', 'Sale')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $need = 3 - $sale->count();

        $rent = collect();
        if ($need > 0) {
            $rent = (clone $base)
                ->where('property_category', 'Rent')
                ->orderByDesc('id')
                ->limit($need)
                ->get();
        }

        $need = 3 - ($sale->count() + $rent->count());

        $offplan = collect();
        if ($need > 0) {
            $offplan = (clone $base)
                ->where('offplan', 1)
                ->orderByDesc('id')
                ->limit($need)
                ->get();
        }

        // ✅ final combined list (Sale first, then Rent, then Offplan)
        $listings = $sale->concat($rent)->concat($offplan)->values();

        // Fetch images (max 10 each) in one query
        $allIds = $listings->pluck('id')->unique()->values()->all();
        $imagesByListingId = collect();

        if (!empty($allIds)) {
            $images = DB::table('listing_images')
                ->select(['listing_id', 'image'])
                ->whereIn('listing_id', $allIds)
                ->where('active', 1)
                ->orderByRaw('COALESCE(sorting, 999999) ASC')
                ->orderBy('id', 'ASC')
                ->get();

            $imagesByListingId = $images
                ->groupBy('listing_id')
                ->map(function ($rows) {
                    return $rows->pluck('image')->take(10)->values()->all(); // ✅ max 10
                });
        }

        // ✅ company contact from .env (works with config cache if you add config/app.php entry)
        // Recommended: add this in config/app.php:
        // 'company_contact' => [
        //   'phone' => env('COMPANY_PHONE'),
        //   'whatsapp' => env('COMPANY_WHATSAPP'),
        //   'email' => env('COMPANY_EMAIL'),
        // ],
        $companyContact = config('app.company_contact');

        // fallback (in case config not added yet)
        if (!$companyContact || !is_array($companyContact)) {
            $companyContact = [
                'phone'    => env('COMPANY_PHONE'),
                'whatsapp' => env('COMPANY_WHATSAPP'),
                'email'    => env('COMPANY_EMAIL'),
            ];
        }

        // attach images + company_contact
        $listings = $listings->map(function ($l) use ($imagesByListingId, $companyContact) {
            $arr = (array) $l;
            $arr['images'] = $imagesByListingId->get($arr['id'], []);
            $arr['company_contact'] = $companyContact;
            return $arr;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'community' => $community_name,
                'listings' => $listings,
            ],
        ], 200);
    }



}
