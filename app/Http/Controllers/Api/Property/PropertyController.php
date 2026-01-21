<?php

namespace App\Http\Controllers\Api\Property;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Currency;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PropertyController extends Controller
{
    use GeneralTrait;

     public function fetchPropertyTypes(Request $request)
    {
        $types = DB::table('property_types')
            ->select(['id', 'text as name', 'slug', 'code'])
            ->whereNull('deleted_at') // important since your table has soft deletes
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $types,
        ], 200);
    }

    public function showOffplanProperties(Request $request)
{
    $perPage = (int) $request->input('per_page', 6);
    if ($perPage <= 0) $perPage = 6;

    // enforce 6 items per page max
    $perPage = min($perPage, 6);

    $page = (int) $request->input('page', 1);
    if ($page <= 0) $page = 1;

    // ✅ filters from request (SAME AS SALE/RENT)
    $minPrice     = $request->input('min_price', null);
    $maxPrice     = $request->input('max_price', null);
    $bedrooms     = $request->input('bedrooms', null);       // array e.g. ["Studio",2,5,7,"7plus"] or ["7+"]
    $bathrooms    = $request->input('bathrooms', null);      // array e.g. ["7plus",5] or ["7+"]
    $search       = $request->input('search', null);         // array of slugs e.g. ["dubai-creek-harbour","azizi-riviera-27"]
    $propertyType = $request->input('property_type', null);  // slug e.g. "apartment"

    // query offplan listings and active
    $query = DB::table('listings')
        ->select([
            'id',
            'reference',
            'bedrooms',
            'bathrooms',
            'price',
            'area',
            'title',
            'description',
            'community',
            'sub_community',
            'property',
            'community_slug',
            'sub_community_slug',
            'property_slug',
            'property_type',
            'active',
            'is_featured',
        ])
        ->where('project_status', 'LIKE', '%off plan%')
        // ->where('property_category', 'Offplan')
        ->where('active', 1);

    // ✅ min/max price filters
    if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
        $query->where('price', '>=', (float) $minPrice);
    }
    if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
        $query->where('price', '<=', (float) $maxPrice);
    }

    // ✅ property_type filter (slug)
    if ($propertyType !== null && is_string($propertyType) && trim($propertyType) !== '') {
        $query->where('property_type_code', trim($propertyType));
    }

    // ✅ search filter: LIKE on community_slug, sub_community_slug, property_slug
    if (is_array($search) && !empty($search)) {
        $searchTerms = array_values(array_filter(array_map(function ($s) {
            return is_string($s) ? trim($s) : null;
        }, $search), function ($s) {
            return $s !== null && $s !== '';
        }));

        if (!empty($searchTerms)) {
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $like = '%' . $term . '%';
                    $q->orWhere('community_slug', 'LIKE', $like)
                      ->orWhere('sub_community_slug', 'LIKE', $like)
                      ->orWhere('property_slug', 'LIKE', $like);
                }
            });
        }
    }

    // ✅ bedrooms filter (Studio + exact nums + 7plus/7+)
    if (is_array($bedrooms) && !empty($bedrooms)) {
        $hasStudio = false;
        $exactNums = [];
        $hasSevenPlus = false;

        foreach ($bedrooms as $b) {
            if ($b === null) continue;

            if (is_string($b)) {
                $val = trim($b);
                if ($val === '') continue;

                if (strcasecmp($val, 'studio') === 0) {
                    $hasStudio = true;
                    continue;
                }

                // ✅ support both "7plus" and "7+"
                if (strcasecmp($val, '7plus') === 0 || preg_match('/^7\s*\+$/', $val)) {
                    $hasSevenPlus = true;
                    continue;
                }

                if (is_numeric($val)) {
                    $exactNums[] = (int) $val;
                    continue;
                }
            }

            if (is_int($b) || is_float($b) || (is_string($b) && is_numeric($b))) {
                $exactNums[] = (int) $b;
            }
        }

        $exactNums = array_values(array_unique($exactNums));

        $query->where(function ($q) use ($hasStudio, $exactNums, $hasSevenPlus) {
            if (!empty($exactNums)) {
                $q->orWhereIn('bedrooms', $exactNums);
            }

            if ($hasStudio) {
                $q->orWhere('bedrooms', 'Studio');
            }

            if ($hasSevenPlus) {
                $q->orWhere('bedrooms', '>', 7);
            }
        });
    }

    // ✅ bathrooms filter (exact nums + 7plus/7+)
    if (is_array($bathrooms) && !empty($bathrooms)) {
        $exactNums = [];
        $hasSevenPlus = false;

        foreach ($bathrooms as $b) {
            if ($b === null) continue;

            if (is_string($b)) {
                $val = trim($b);
                if ($val === '') continue;

                // ✅ support both "7plus" and "7+"
                if (strcasecmp($val, '7plus') === 0 || preg_match('/^7\s*\+$/', $val)) {
                    $hasSevenPlus = true;
                    continue;
                }

                if (is_numeric($val)) {
                    $exactNums[] = (int) $val;
                    continue;
                }
            }

            if (is_int($b) || is_float($b) || (is_string($b) && is_numeric($b))) {
                $exactNums[] = (int) $b;
            }
        }

        $exactNums = array_values(array_unique($exactNums));

        $query->where(function ($q) use ($exactNums, $hasSevenPlus) {
            if (!empty($exactNums)) {
                $q->orWhereIn('bathrooms', $exactNums);
            }
            if ($hasSevenPlus) {
                $q->orWhere('bathrooms', '>', 7);
            }
        });
    }

    // keep existing ordering
    $query->orderByDesc('id');

    $paginator = $query->paginate($perPage, ['*'], 'page', $page);

    if ($paginator->total() === 0) {
        return response()->json([
            'success' => true,
            'count' => 0,
            'data' => [],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
        ], 200);
    }

    $items = collect($paginator->items());
    $listingIds = $items->pluck('id')->values()->all();

    $imagesRows = DB::table('listing_images')
        ->select(['listing_id', 'image', 'sorting'])
        ->whereIn('listing_id', $listingIds)
        ->orderBy('listing_id')
        ->orderBy('sorting')
        ->get();

    $imagesByListing = [];
    foreach ($imagesRows as $row) {
        $imagesByListing[$row->listing_id][] = $row->image;
    }

    // ✅ keep response keys SAME as your sale/rent response (frontend expects sale_listings)
    $sale_listings = $items->map(function ($listing) use ($imagesByListing) {
        return [
            'reference'      => $listing->reference,
            'id'             => $listing->id,
            'bedrooms'       => $listing->bedrooms,
            'bathrooms'      => $listing->bathrooms,
            'price'          => $listing->price,
            'area'           => $listing->area,
            'title'          => $listing->title,
            'description'    => $listing->description,
            'community'      => $listing->community,
            'sub_community'  => $listing->sub_community,
            'property'       => $listing->property,
            'community_slug' => $listing->community_slug ?? null,
            'sub_community_slug' => $listing->sub_community_slug ?? null,
            'property_slug'  => $listing->property_slug ?? null,
            'property_type'  => $listing->property_type ?? null,
            'active'         => $listing->active,
            'is_featured'    => $listing->is_featured,
            'images'         => $imagesByListing[$listing->id] ?? [],

            'company_contact' => [
                'phone'    => env('COMPANY_PHONE'),
                'whatsapp' => env('COMPANY_WHATSAPP'),
                'email'    => env('COMPANY_EMAIL'),
            ],
        ];
    })->values();

    return response()->json([
        'success' => true,
        'data' => [
            'sale_listings' => $sale_listings
        ],
        'pagination' => [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ],
    ], 200);
}


  public function listingDetails($reference)
{
    $listing = DB::table('listings')
        ->leftJoin('agents', 'agents.id', '=', 'listings.agent_id')
        ->where('listings.reference', $reference)
        ->select(
            'listings.*',
            'agents.name as employee_name',
            'agents.slug as employee_slug',
            'agents.image as employee_profile_picture',
            'agents.orn as employee_orn',
            'agents.email as employee_email',
            'agents.phone as employee_phone',
        )
        ->first();

    if (!$listing) {
        return response()->json([
            'status' => false,
            'message' => 'Listing not found',
        ], 404);
    }

    $images = DB::table('listing_images')
        ->where('listing_id', $listing->id)
        ->orderByRaw('sorting IS NULL, sorting ASC')
        ->pluck('image')
        ->values();

     $agent_details = DB::table('employees')
        ->where('crm_name', $listing->agent)
        ->select(['name', 'slug', 'position', 'profile_picture', 'brn', 'description'])
        ->first();

    $private_amenities = DB::table('private_amenity_listings')
        ->join('private_amenities', 'private_amenities.code', '=', 'private_amenity_listings.amenity_code')
        ->where('private_amenity_listings.listing_reference', $listing->reference)
        ->pluck('private_amenities.name')
        ->values();

        $commercial_amenities = DB::table('commercial_amenity_listings')
        ->join('commercial_amenities', 'commercial_amenities.code', '=', 'commercial_amenity_listings.amenity_code')
        ->where('commercial_amenity_listings.listing_reference', $listing->reference)
        ->pluck('commercial_amenities.name')
        ->values();

    $employee = [
        'name'            => $listing->employee_name,
        'slug'            => $listing->employee_slug,
        'orn'             => $listing->employee_orn,
        'profile_picture' => $listing->employee_profile_picture,
        'email'           => $listing->employee_email,
        'phone'           => $listing->employee_phone,
    ];

    unset(
        $listing->employee_name,
        $listing->employee_slug,
        $listing->employee_position,
        $listing->employee_profile_picture,
        $listing->employee_email,
        $listing->employee_phone,
        $listing->employee_whatsapp
    );

    return response()->json([
        'status' => true,
        'data' => [
            'listing'   => $listing,
            'employee'  => $employee,
            'agents' => $agent_details,
            'images'    => $images,
            'private_amenities' => $private_amenities,
            'commercial_amenities' => $commercial_amenities,
        ],
    ]);
}



   public function showRentProperties(Request $request)
{
    $perPage = (int) $request->input('per_page', 6);
    if ($perPage <= 0) $perPage = 6;

    // enforce 6 items per page max
    $perPage = min($perPage, 6);

    $page = (int) $request->input('page', 1);
    if ($page <= 0) $page = 1;

    // ✅ filters from request
    $minPrice     = $request->input('min_price', null);
    $maxPrice     = $request->input('max_price', null);
    $bedrooms     = $request->input('bedrooms', null);      // array e.g. ["Studio",2,5,7,"7plus"]
    $bathrooms    = $request->input('bathrooms', null);     // array e.g. ["7plus",5]
    $search       = $request->input('search', null);        // array of slugs e.g. ["dubai-creek-harbour","azizi-riviera-27"]
    $propertyType = $request->input('property_type', null); // slug e.g. "apartment"

    // query rent listings (property_category = 'Rent') and active
    $query = DB::table('listings')
        ->select([
            'id',
            'reference',
            'bedrooms',
            'bathrooms',
            'price',
            'area',
            'title',
            'description',
            'community',
            'sub_community',
            'property',
            'community_slug',
            'sub_community_slug',
            'property_slug',
            'property_type',
            'active',
            'is_featured',
        ])
        ->where('property_category', 'Rent')
        ->where('active', 1);

    // ✅ min/max price filters
    if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
        $query->where('price', '>=', (float) $minPrice);
    }
    if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
        $query->where('price', '<=', (float) $maxPrice);
    }

    // ✅ property_type filter (slug)
    if ($propertyType !== null && is_string($propertyType) && trim($propertyType) !== '') {
        $query->where('property_type_code', trim($propertyType));
    }

    // ✅ search filter: LIKE on community_slug, sub_community_slug, property_slug
    if (is_array($search) && !empty($search)) {
        $searchTerms = array_values(array_filter(array_map(function ($s) {
            return is_string($s) ? trim($s) : null;
        }, $search), function ($s) {
            return $s !== null && $s !== '';
        }));

        if (!empty($searchTerms)) {
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $like = '%' . $term . '%';
                    $q->orWhere('community_slug', 'LIKE', $like)
                      ->orWhere('sub_community_slug', 'LIKE', $like)
                      ->orWhere('property_slug', 'LIKE', $like);
                }
            });
        }
    }

    // ✅ bedrooms filter:
    // - Studio means DB value "Studio"
    // - "7plus" means bedrooms > 7
    // - rest numeric exact matches
    if (is_array($bedrooms) && !empty($bedrooms)) {
        $hasStudio = false;
        $exactNums = [];
        $hasSevenPlus = false;

        foreach ($bedrooms as $b) {
            if ($b === null) continue;

            if (is_string($b)) {
                $val = trim($b);
                if ($val === '') continue;

                if (strcasecmp($val, 'studio') === 0) {
                    $hasStudio = true;
                    continue;
                }

                // support both "7plus" and "7+"
                if (strcasecmp($val, '7plus') === 0 || preg_match('/^7\s*\+$/', $val)) {
                    $hasSevenPlus = true;
                    continue;
                }

                if (is_numeric($val)) {
                    $exactNums[] = (int) $val;
                    continue;
                }
            }

            if (is_int($b) || is_float($b) || (is_string($b) && is_numeric($b))) {
                $exactNums[] = (int) $b;
            }
        }

        $exactNums = array_values(array_unique($exactNums));

        $query->where(function ($q) use ($hasStudio, $exactNums, $hasSevenPlus) {
            if (!empty($exactNums)) {
                $q->orWhereIn('bedrooms', $exactNums);
            }

            if ($hasStudio) {
                $q->orWhere('bedrooms', 'Studio');
            }

            if ($hasSevenPlus) {
                $q->orWhere('bedrooms', '>', 7);
            }
        });
    }

    // ✅ bathrooms filter:
    // - "7plus" means bathrooms > 7
    // - rest numeric exact matches
    if (is_array($bathrooms) && !empty($bathrooms)) {
        $exactNums = [];
        $hasSevenPlus = false;

        foreach ($bathrooms as $b) {
            if ($b === null) continue;

            if (is_string($b)) {
                $val = trim($b);
                if ($val === '') continue;

                // support both "7plus" and "7+"
                if (strcasecmp($val, '7plus') === 0 || preg_match('/^7\s*\+$/', $val)) {
                    $hasSevenPlus = true;
                    continue;
                }

                if (is_numeric($val)) {
                    $exactNums[] = (int) $val;
                    continue;
                }
            }

            if (is_int($b) || is_float($b) || (is_string($b) && is_numeric($b))) {
                $exactNums[] = (int) $b;
            }
        }

        $exactNums = array_values(array_unique($exactNums));

        $query->where(function ($q) use ($exactNums, $hasSevenPlus) {
            if (!empty($exactNums)) {
                $q->orWhereIn('bathrooms', $exactNums);
            }
            if ($hasSevenPlus) {
                $q->orWhere('bathrooms', '>', 7);
            }
        });
    }

    // keep existing ordering
    $query->orderByDesc('id');

    $paginator = $query->paginate($perPage, ['*'], 'page', $page);

    if ($paginator->total() === 0) {
        return response()->json([
            'success' => true,
            'count' => 0,
            'data' => [],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
        ], 200);
    }

    $items = collect($paginator->items());
    $listingIds = $items->pluck('id')->values()->all();

    $imagesRows = DB::table('listing_images')
        ->select(['listing_id', 'image', 'sorting'])
        ->whereIn('listing_id', $listingIds)
        ->orderBy('listing_id')
        ->orderBy('sorting')
        ->get();

    $imagesByListing = [];
    foreach ($imagesRows as $row) {
        $imagesByListing[$row->listing_id][] = $row->image;
    }

    // ✅ keep response keys SAME as your sale response (frontend already expects sale_listings)
    $sale_listings = $items->map(function ($listing) use ($imagesByListing) {
        return [
            'reference'      => $listing->reference,
            'id'             => $listing->id,
            'bedrooms'       => $listing->bedrooms,
            'bathrooms'      => $listing->bathrooms,
            'price'          => $listing->price,
            'area'           => $listing->area,
            'title'          => $listing->title,
            'description'    => $listing->description,
            'community'      => $listing->community,
            'sub_community'  => $listing->sub_community,
            'property'       => $listing->property,
            'community_slug' => $listing->community_slug ?? null,
            'sub_community_slug' => $listing->sub_community_slug ?? null,
            'property_slug'  => $listing->property_slug ?? null,
            'property_type'  => $listing->property_type ?? null,
            'active'         => $listing->active,
            'is_featured'    => $listing->is_featured,
            'images'         => $imagesByListing[$listing->id] ?? [],

            'company_contact' => [
                'phone'    => env('COMPANY_PHONE'),
                'whatsapp' => env('COMPANY_WHATSAPP'),
                'email'    => env('COMPANY_EMAIL'),
            ],
        ];
    })->values();

    return response()->json([
        'success' => true,
        'data' => [
            'sale_listings' => $sale_listings
        ],
        'pagination' => [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ],
    ], 200);
}


  public function showSaleProperties(Request $request)
{
    $perPage = (int) $request->input('per_page', 6);
    if ($perPage <= 0) $perPage = 6;

    // enforce 6 items per page max
    $perPage = min($perPage, 6);

    $page = (int) $request->input('page', 1);
    if ($page <= 0) $page = 1;

    // ✅ filters from request
    $minPrice     = $request->input('min_price', null);
    $maxPrice     = $request->input('max_price', null);
    $bedrooms     = $request->input('bedrooms', null);     // array e.g. ["Studio",2,5,7,"7plus"]
    $bathrooms    = $request->input('bathrooms', null);    // array e.g. ["7plus",5]
    $search       = $request->input('search', null);       // array of slugs e.g. ["dubai-creek-harbour","azizi-riviera-27"]
    $propertyType = $request->input('property_type', null); // slug e.g. "apartment"

    // query sale listings (property_category = 'Sale') and active
    $query = DB::table('listings')
        ->select([
            'id',
            'reference',
            'bedrooms',
            'bathrooms',
            'price',
            'area',
            'title',
            'description',
            'community',
            'sub_community',
            'property',
            'community_slug',
            'sub_community_slug',
            'property_slug',
            'property_type',
            'active',
            'is_featured',
        ])
        ->where('property_category', 'Sale')
        ->where('active', 1);

    // ✅ min/max price filters
    if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
        $query->where('price', '>=', (float) $minPrice);
    }
    if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
        $query->where('price', '<=', (float) $maxPrice);
    }

    // ✅ property_type filter (slug)
    if ($propertyType !== null && is_string($propertyType) && trim($propertyType) !== '') {
        $query->where('property_type_code', trim($propertyType));
    }

    // ✅ search filter: LIKE on community_slug, sub_community_slug, property_slug
    if (is_array($search) && !empty($search)) {
        $searchTerms = array_values(array_filter(array_map(function ($s) {
            return is_string($s) ? trim($s) : null;
        }, $search), function ($s) {
            return $s !== null && $s !== '';
        }));

        if (!empty($searchTerms)) {
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $like = '%' . $term . '%';
                    $q->orWhere('community_slug', 'LIKE', $like)
                      ->orWhere('sub_community_slug', 'LIKE', $like)
                      ->orWhere('property_slug', 'LIKE', $like);
                }
            });
        }
    }

    // ✅ bedrooms filter:
    // - Studio means DB value "Studio"
    // - "7plus" means bedrooms > 7 (no real value in DB)
    // - rest numeric exact matches
    if (is_array($bedrooms) && !empty($bedrooms)) {
        $hasStudio = false;
        $exactNums = [];
        $hasSevenPlus = false;

        foreach ($bedrooms as $b) {
            if ($b === null) continue;

            if (is_string($b)) {
                $val = trim($b);

                if ($val === '') continue;

                if (strcasecmp($val, 'studio') === 0) {
                    $hasStudio = true;
                    continue;
                }

                // ✅ support both "7plus" and "7+"
                if (strcasecmp($val, '7plus') === 0 || preg_match('/^7\s*\+$/', $val)) {
                    $hasSevenPlus = true;
                    continue;
                }

                // numeric string
                if (is_numeric($val)) {
                    $exactNums[] = (int) $val;
                    continue;
                }
            }

            if (is_int($b) || is_float($b) || (is_string($b) && is_numeric($b))) {
                $exactNums[] = (int) $b;
            }
        }

        $exactNums = array_values(array_unique($exactNums));

        $query->where(function ($q) use ($hasStudio, $exactNums, $hasSevenPlus) {
            if (!empty($exactNums)) {
                $q->orWhereIn('bedrooms', $exactNums);
            }

            if ($hasStudio) {
                $q->orWhere('bedrooms', 'Studio');
            }

            if ($hasSevenPlus) {
                $q->orWhere('bedrooms', '>', 7);
            }
        });
    }

    // ✅ bathrooms filter:
    // - "7plus" means bathrooms > 7 (no real value in DB)
    // - rest numeric exact matches
    if (is_array($bathrooms) && !empty($bathrooms)) {
        $exactNums = [];
        $hasSevenPlus = false;

        foreach ($bathrooms as $b) {
            if ($b === null) continue;

            if (is_string($b)) {
                $val = trim($b);

                if ($val === '') continue;

                // ✅ support both "7plus" and "7+"
                if (strcasecmp($val, '7plus') === 0 || preg_match('/^7\s*\+$/', $val)) {
                    $hasSevenPlus = true;
                    continue;
                }

                if (is_numeric($val)) {
                    $exactNums[] = (int) $val;
                    continue;
                }
            }

            if (is_int($b) || is_float($b) || (is_string($b) && is_numeric($b))) {
                $exactNums[] = (int) $b;
            }
        }

        $exactNums = array_values(array_unique($exactNums));

        $query->where(function ($q) use ($exactNums, $hasSevenPlus) {
            if (!empty($exactNums)) {
                $q->orWhereIn('bathrooms', $exactNums);
            }
            if ($hasSevenPlus) {
                $q->orWhere('bathrooms', '>', 7);
            }
        });
    }

    // keep existing ordering
    $query->orderByDesc('id');

    $paginator = $query->paginate($perPage, ['*'], 'page', $page);

    if ($paginator->total() === 0) {
        return response()->json([
            'success' => true,
            'count' => 0,
            'data' => [],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
        ], 200);
    }

    $items = collect($paginator->items());
    $listingIds = $items->pluck('id')->values()->all();

    $imagesRows = DB::table('listing_images')
        ->select(['listing_id', 'image', 'sorting'])
        ->whereIn('listing_id', $listingIds)
        ->orderBy('listing_id')
        ->orderBy('sorting')
        ->get();

    $imagesByListing = [];
    foreach ($imagesRows as $row) {
        $imagesByListing[$row->listing_id][] = $row->image;
    }

    $sale_listings = $items->map(function ($listing) use ($imagesByListing) {
        return [
            'reference'      => $listing->reference,
            'id'             => $listing->id,
            'bedrooms'       => $listing->bedrooms,
            'bathrooms'      => $listing->bathrooms,
            'price'          => $listing->price,
            'area'           => $listing->area,
            'title'          => $listing->title,
            'description'    => $listing->description,
            'community'      => $listing->community,
            'sub_community'  => $listing->sub_community,
            'property'       => $listing->property,
            'community_slug' => $listing->community_slug ?? null,
            'sub_community_slug' => $listing->sub_community_slug ?? null,
            'property_slug'  => $listing->property_slug ?? null,
            'property_type'  => $listing->property_type ?? null,
            'active'         => $listing->active,
            'is_featured'    => $listing->is_featured,
            'images'         => $imagesByListing[$listing->id] ?? [],

            'company_contact' => [
                'phone'    => env('COMPANY_PHONE'),
                'whatsapp' => env('COMPANY_WHATSAPP'),
                'email'    => env('COMPANY_EMAIL'),
            ],
        ];
    })->values();

    return response()->json([
        'success' => true,
        'data' => [
            'sale_listings' => $sale_listings
        ],
        'pagination' => [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ],
    ], 200);
}

public function showFeaturedPropertiesWithType(Request $request)
{
    // ✅ POST params
    $reference         = trim((string) $request->input('reference', ''));
    $propertyCategory  = trim((string) $request->input('property_category', ''));
    $projectStatus     = trim((string) $request->input('project_status', ''));

    if ($reference === '') {
        return response()->json([
            'success' => false,
            'message' => 'reference is required',
            'data'    => [],
        ], 422);
    }

    if ($propertyCategory === '') {
        return response()->json([
            'success' => false,
            'message' => 'property_category is required',
            'data'    => [],
        ], 422);
    }

    // ✅ hard cap 4 (same behavior)
    $limit = 4;

    // 1) Find listing by reference + get property_type
    $baseListing = DB::table('listings')
        ->select(['reference', 'property_type'])
        ->where('reference', $reference)
        ->first();

    if (!$baseListing) {
        return response()->json([
            'success' => false,
            'message' => 'Listing not found for given reference',
            'data'    => [],
        ], 404);
    }

    $propertyType = trim((string) ($baseListing->property_type ?? ''));

    if ($propertyType === '') {
        return response()->json([
            'success' => false,
            'message' => 'property_type not found for this listing',
            'data'    => [],
        ], 422);
    }

    // 2) Build allowed property_type rules
    $typeLower = mb_strtolower($propertyType);

    if ($typeLower === 'villa' || $typeLower === 'townhouse') {
        $allowedTypes = ['Villa', 'Townhouse'];
    } elseif ($typeLower === 'apartment' || $typeLower === 'hotel apartment' || $typeLower === 'hotel_apartment') {
        $allowedTypes = ['Apartment', 'Hotel Apartment', 'Hotel_Apartment'];
    } else {
        $allowedTypes = [$propertyType];
    }

    // 3) Build query
    $query = DB::table('listings')
        ->select([
            'id',
            'unit_id',
            'reference',
            'bedrooms',
            'bathrooms',
            'price',
            'area',
            'title',
            'community',
            'sub_community',
            'property',
            'active',
            'is_featured',
            'property_type',
            'property_category',
            'project_status',
        ])
        ->where('active', 1)
        ->whereIn('property_type', $allowedTypes)
        ->where('property_category', $propertyCategory)
        ->where('reference', '!=', $reference);

    // 4) Off-plan matching rule
    // If incoming project_status contains "off plan" (any casing),
    // then only return records whose project_status also contains "off plan".
    if (stripos($projectStatus, 'off plan') !== false || stripos($projectStatus, 'off-plan') !== false) {
        $query->where(function ($q) {
            $q->whereRaw('LOWER(project_status) LIKE ?', ['%off plan%'])
              ->orWhereRaw('LOWER(project_status) LIKE ?', ['%off-plan%']);
        });
    }

    $listings = $query
        ->orderByDesc('id')
        ->limit($limit)
        ->get();

    if ($listings->isEmpty()) {
        return response()->json([
            'success' => true,
            'count'   => 0,
            'data'    => ["featured_listings" => []],
        ], 200);
    }

    // ✅ images: listing_images.listing_id = listings.id
    $listingIds = $listings->pluck('id')->values()->all();

    $imagesRows = DB::table('listing_images')
        ->select(['listing_id', 'image', 'sorting'])
        ->whereIn('listing_id', $listingIds)
        ->where('active', 1)
        ->orderBy('sorting')
        ->get();

    $imagesByListing = [];
    foreach ($imagesRows as $row) {
        $imagesByListing[$row->listing_id][] = $row->image;
    }

    $featured_listings = $listings->map(function ($listing) use ($imagesByListing) {
        return [
            'reference'       => $listing->reference,
            'id'              => $listing->id,
            'bedrooms'        => $listing->bedrooms,
            'bathrooms'       => $listing->bathrooms,
            'price'           => $listing->price,
            'area'            => $listing->area,
            'title'           => $listing->title,
            'community'       => $listing->community,
            'sub_community'   => $listing->sub_community,
            'property'        => $listing->property,
            'active'          => $listing->active,
            'is_featured'     => $listing->is_featured,
            'property_type'   => $listing->property_type,
            'property_category' => $listing->property_category,
            'project_status'  => $listing->project_status,
            'images'          => $imagesByListing[$listing->id] ?? [],

            'company_contact' => [
                'phone'    => env('COMPANY_PHONE'),
                'whatsapp' => env('COMPANY_WHATSAPP'),
                'email'    => env('COMPANY_EMAIL'),
            ],
        ];
    });

    return response()->json([
        'success' => true,
        'data'    => ["featured_listings" => $featured_listings],
    ], 200);
}


   public function showFeaturedProperties(Request $request)
{
    // ✅ limit handling (min any, max capped)
    $limit = (int) $request->query('limit', 4);
    if ($limit <= 0) $limit = 4;

    $MAX_LIMIT = 4;
    if ($limit > $MAX_LIMIT) $limit = $MAX_LIMIT;

    $listings = DB::table('listings')
        ->select([
            'id',
            'unit_id',
            'reference',
            'bedrooms',
            'bathrooms',
            'price',
            'area',
            'title',
            'community',
            'sub_community',
            'property',
            'active',
            'is_featured',
        ])
        ->where('is_featured', 1)
        ->where('active', 1)
        ->orderByDesc('id')
        ->limit($limit)
        ->get();

    if ($listings->isEmpty()) {
        return response()->json([
            'success' => true,
            'count' => 0,
            'data' => [],
        ], 200);
    }

    $listingIds = $listings->pluck('unit_id')->values()->all();

    $imagesRows = DB::table('listing_images')
        ->select(['listing_id', 'image', 'sorting'])
        ->whereIn('listing_id', $listingIds)
        ->where('active', 1)
        // ->orderBy('listing_id')
        ->orderBy('sorting')
        ->get();

    // ✅ Group images by listing_id
    $imagesByListing = [];
    foreach ($imagesRows as $row) {
        $imagesByListing[$row->listing_id][] = $row->image;
    }

    // ✅ Attach images + company_contact to each listing
    $featured_listings = $listings->map(function ($listing) use ($imagesByListing) {
        return [
            'reference'      => $listing->reference,
            'id'             => $listing->id,
            'bedrooms'       => $listing->bedrooms,
            'bathrooms'      => $listing->bathrooms,
            'price'          => $listing->price,
            'area'           => $listing->area,
            'title'          => $listing->title,
            'community'      => $listing->community,
            'sub_community'  => $listing->sub_community,
            'property'       => $listing->property,
            'active'         => $listing->active,
            'is_featured'    => $listing->is_featured,
            'images'         => $imagesByListing[$listing->id] ?? [],

            // ✅ company contact inside each listing
            'company_contact' => [
                'phone'    => env('COMPANY_PHONE'),
                'whatsapp' => env('COMPANY_WHATSAPP'),
                'email'    => env('COMPANY_EMAIL'),
            ],
        ];
    });

    return response()->json([
        'success' => true,
        'data' => ["featured_listings" => $featured_listings],
    ], 200);
}


    public function getFilterOptions(Request $request)
    {
        try {
            // Get the exchange rate if passed through the header.
            $currencyCode = $request->header('X-Currency', 'AED');
            $currency     = Currency::where('title', $currencyCode)->first();
            $rate         = $currency?->rate ?? 1;

            // Collect property data.
            $properties = Property::select('area','price','num_bedroom')->get();

            $areas  = $properties->pluck('area')->filter()->unique()->sort()->values();
            $prices = $properties->pluck('price')->map(fn($p) => round($p * $rate, 2))->unique()->sort()->values();
            $beds   = $properties->pluck('num_bedroom')->filter()->unique()->sort()->values();

            // Helper function to build min/max options.
            $buildOptions = function($col) {
                $count = $col->count();
                if ($count <= 1) {
                    return [
                        'minOptions' => $col,
                        'maxOptions' => $col,
                    ];
                }
                return [
                    'minOptions' => $col->slice(0, $count - 1)->values(),
                    'maxOptions' => $col->slice(1)->values(),
                ];
            };

            $areasOpts  = $buildOptions($areas);
            $pricesOpts = $buildOptions($prices);
            $bedsOpts   = $buildOptions($beds);


            $types = PropertyType::select('id', 'name')->get()->map(fn($t) => [
                'id'   => $t->id,
                'name' => $t->name,   // Will return the name based on the current language thanks to HasTranslations
            ]);

            return $this->apiResponse([
                'areas'          => array_merge(['values' => $areas], $areasOpts),
                'prices'         => array_merge(['values' => $prices, 'currency' => $currency?->symbol ?? '$'], $pricesOpts),
                'bedrooms'       => array_merge(['values' => $beds], $bedsOpts),
                'property_types' => $types,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }




    public function searchProperty(Request $request)
    {
        try {
            $query = Property::query();

            // General search in the text (only in the property title).
            if ($search = $request->get('search')) {
                $query->where('title', 'LIKE', "%{$search}%");
            }

            if ($request->filled('property_ids')) {
                $ids = $request->get('property_ids');
                if (is_string($ids)) {
                    $ids = explode(',', $ids);
                }
                if (!empty($ids)) {
                    $query->whereIn('id', $ids);
                }
            }

            if ($request->filled('search_keywords')) {
                $searchKeywords = $request->get('search_keywords');

                // Make sure it's an array, if it's a string, split it by spaces.
                if (!is_array($searchKeywords)) {
                    $searchKeywords = explode(' ', $searchKeywords);
                }

                $query->where(function ($q) use ($searchKeywords) {
                    foreach ($searchKeywords as $keyword) {
                        $q->orWhere('title', 'LIKE', "%{$keyword}%");
                    }
                });
            }

            // Default: show properties not for sale.
            if (! $request->filled('is_sale')) {
                $query->where('is_sale', 0);
            } elseif ($request->filled('is_sale')) {
                $query->where('is_sale', $request->boolean('is_sale'));
            }

            // Area: only one value.
            if ($request->filled('area')) {
                $query->where('area', $request->get('area'));
            }

            // Bedrooms: range (min / max).
            if ($request->filled('bedroom_min')) {
                $query->where('num_bedroom', '>=', $request->get('bedroom_min'));
            }
            if ($request->filled('bedroom_max')) {
                $query->where('num_bedroom', '<=', $request->get('bedroom_max'));
            }

            // Handle the exchange rate.
            $currencyCode = $request->header('X-Currency', 'AED');
            $currency     = \App\Models\Currency::where('title', $currencyCode)->first();
            $rate         = $currency?->rate ?? 1;

            if ($request->filled('price_min')) {
                $query->where('price', '>=', $request->get('price_min') / $rate);
            }
            if ($request->filled('price_max')) {
                $query->where('price', '<=', $request->get('price_max') / $rate);
            }

            // The rest of the filters as before.
            if ($request->filled('type_id')) {
                $query->where('type_id', $request->get('type_id'));
            }
            if ($request->filled('property_type_id')) {
                $query->where('property_type_id', $request->get('property_type_id'));
            }
            if ($request->filled('region_id')) {
                $query->where('region_id', $request->get('region_id'));
            }
            // Filter by region name (supports search in all languages).
            if ($request->filled('region_name')) {
                $regionName = $request->get('region_name');
                $query->whereHas('region', function($q) use ($regionName) {
                    $q->where(function($query) use ($regionName) {
                        $query->where('name->en', 'LIKE', "%{$regionName}%")
                              ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                    });
                });
            }
            // الفلترة بعدة مناطق (OR - العلاقة بينهم أو)
            if ($request->filled('region_names')) {
                $regionNames = $request->get('region_names');
                
                // إذا كانت نص، قسمها بالفواصل
                if (is_string($regionNames)) {
                    $regionNames = array_map('trim', explode(',', $regionNames));
                }
                
                // تأكد أنها مصفوفة
                if (is_array($regionNames) && !empty($regionNames)) {
                    $query->whereHas('region', function($q) use ($regionNames) {
                        $q->where(function($query) use ($regionNames) {
                            foreach ($regionNames as $index => $regionName) {
                                if ($index === 0) {
                                    $query->where(function($subQuery) use ($regionName) {
                                        $subQuery->where('name->en', 'LIKE', "%{$regionName}%")
                                                 ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                                    });
                                } else {
                                    $query->orWhere(function($subQuery) use ($regionName) {
                                        $subQuery->where('name->en', 'LIKE', "%{$regionName}%")
                                                 ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                                    });
                                }
                            }
                        });
                    });
                }
            }
            if ($request->filled('developer_id')) {
                $query->where('developer_id', $request->get('developer_id'));
            }
            // البحث بالموقع (نصف القطر)
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $radius = $request->get('radius', 10); // افتراضي 10 كم
                $query = $query->withinRadius(
                    $request->get('latitude'),
                    $request->get('longitude'),
                    $radius
                );
            }

            if ($request->filled('sort') && in_array($request->get('sort'), ['min', 'max'])) {
                $direction = $request->get('sort') === 'min' ? 'asc' : 'desc';
                $query->orderBy('price', $direction);
            } elseif ($request->filled('latitude') && $request->filled('longitude')) {
                // إذا كان هناك بحث بالموقع، رتب حسب المسافة
                // (تم ترتيبها بالفعل في withinRadius)
            } else {
                $query->latest();
            }
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 12);

            $properties = $query->paginate($perPage, ['*'], 'page', $page);

            return $this->apiResponse([
                'properties' => \App\Http\Resources\Model\PropertyResource::collection($properties),
                'pagination' => [
                    'current_page'   => $properties->currentPage(),
                    'requested_page' => (int) $page, // توضيح الصفحة المطلوبة صراحة
                    'per_page'       => $properties->perPage(),
                    'total'          => $properties->total(),
                    'last_page'      => $properties->lastPage(),
                    'next_page_url'  => $properties->nextPageUrl(),
                    'prev_page_url'  => $properties->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function allProperties(Request $request)
    {
        try {
            $query = Property::query();

            if ($search = $request->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%");
//                        ->orWhereHas('region', fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
//                        ->orWhereHas('developer', fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
//                        ->orWhereHas('agent', fn($q) => $q->where('name', 'LIKE', "%{$search}%"));

                });
            }

            if ($request->filled('property_ids')) {
                $ids = $request->get('property_ids');
                if (is_string($ids)) {
                    $ids = explode(',', $ids);
                }
                if (!empty($ids)) {
                    $query->whereIn('id', $ids);
                }
            }

            if ($request->filled('search_keywords')) {
                $searchKeywords = $request->get('search_keywords');

                // تأكد أنها مصفوفة، لو إجت نص قسمها بالفراغات
                if (!is_array($searchKeywords)) {
                    $searchKeywords = explode(' ', $searchKeywords);
                }

                $query->where(function ($q) use ($searchKeywords) {
                    foreach ($searchKeywords as $keyword) {
                        $q->orWhere('title', 'LIKE', "%{$keyword}%");
                    }
                });
            }

            if ($request->filled('is_home')) {
                $query->where('is_home', $request->boolean('is_home'));
            }
            if ($request->filled('property_type_id')) {
                $query->where('property_type_id', $request->get('property_type_id'));
            }

            if ($request->filled('is_finish')) {
                $query->where('is_finish', $request->boolean('is_finish'));
            }

            if ($request->filled('region_id')) {
                $query->where('region_id', $request->get('region_id'));
            }
            // الفلترة حسب اسم المنطقة (يدعم البحث في جميع اللغات)
            if ($request->filled('region_name')) {
                $regionName = $request->get('region_name');
                $query->whereHas('region', function($q) use ($regionName) {
                    $q->where(function($query) use ($regionName) {
                        $query->where('name->en', 'LIKE', "%{$regionName}%")
                              ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                    });
                });
            }
            // الفلترة بعدة مناطق (OR - العلاقة بينهم أو)
            if ($request->filled('region_names')) {
                $regionNames = $request->get('region_names');
                
                // إذا كانت نص، قسمها بالفواصل
                if (is_string($regionNames)) {
                    $regionNames = array_map('trim', explode(',', $regionNames));
                }
                
                // تأكد أنها مصفوفة
                if (is_array($regionNames) && !empty($regionNames)) {
                    $query->whereHas('region', function($q) use ($regionNames) {
                        $q->where(function($query) use ($regionNames) {
                            foreach ($regionNames as $index => $regionName) {
                                if ($index === 0) {
                                    $query->where(function($subQuery) use ($regionName) {
                                        $subQuery->where('name->en', 'LIKE', "%{$regionName}%")
                                                 ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                                    });
                                } else {
                                    $query->orWhere(function($subQuery) use ($regionName) {
                                        $subQuery->where('name->en', 'LIKE', "%{$regionName}%")
                                                 ->orWhere('name->ar', 'LIKE', "%{$regionName}%");
                                    });
                                }
                            }
                        });
                    });
                }
            }

            if (! $request->filled('is_sale')) {
                $query->where('is_sale', 0);
            }

            if ($request->filled('is_sale')) {
                $query->where('is_sale', $request->boolean('is_sale'));
            }

            if ($request->filled('agent_id')) {
                $query->where('agent_id', $request->get('agent_id'));
            }

            if ($request->filled('developer_id')) {
                $query->where('developer_id', $request->get('developer_id'));
            }

            if ($request->filled('service_id')) {
                $query->where('service_id', $request->get('service_id'));
            }

            if ($request->filled('type_id')) {
                $query->where('type_id', $request->get('type_id'));
            }
            if ($request->filled('sort') && in_array($request->get('sort'), ['min', 'max'])) {
                $direction = $request->get('sort') === 'min' ? 'asc' : 'desc';
                $query->orderBy('price', $direction);
            }else{
                $query->latest();
            }

            // تحميل العلاقات الأساسية فقط لتحسين الأداء
            $properties = $query->with([
                'agent:id,name,email,phone,image',
                'developer:id,name,logo',
                'propertyType:id,name',
                'type:id,name',
                'region:id,name'
            ])->get();

            return $this->apiResponse([
                'properties' => \App\Http\Resources\Model\PropertyResource::collection($properties),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


    public function show(Request $request)
    {
        try {
            $property = Property::with([
                'amenities', 
                'floorplans', 
                'nearbyPlaces', 
                'uniquePoints', 
                'paymentSchedules', 
                'faqs'
            ])->findOrFail($request->property_id);
            
            return $this->apiResponse([
                'property' => new \App\Http\Resources\Model\PropertyResource($property),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function showBySlug(Request $request, $slug)
    {
        try {
            $property = Property::with([
                'amenities', 
                'floorplans', 
                'nearbyPlaces', 
                'uniquePoints', 
                'paymentSchedules', 
                'faqs'
            ])->where('slug', $slug)->firstOrFail();
            
            return $this->apiResponse([
                'property' => new \App\Http\Resources\Model\PropertyResource($property),
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * البحث عن العقارات بالموقع الجغرافي
     */
    public function searchByLocation(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return $this->requiredField($validator->errors()->first());
            }

            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $radius = $request->get('radius', 10); // افتراضي 10 كم

            $properties = Property::withinRadius($latitude, $longitude, $radius)
                ->with(['type', 'region', 'developer'])
                ->get();

            return $this->apiResponse([
                'properties' => \App\Http\Resources\Model\PropertyResource::collection($properties),
                'search_center' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ],
                'radius_km' => $radius,
                'total_found' => $properties->count()
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

}
