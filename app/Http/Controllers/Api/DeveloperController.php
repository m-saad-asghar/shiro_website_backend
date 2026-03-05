<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DeveloperController extends Controller
{
    public function updateDeveloper(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'developer_id'     => ['required', 'integer', 'min:1'],
            'name'             => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'max:256'],
            'email'            => ['nullable', 'email', 'max:255'],
            'description'      => ['required', 'string'],
            'active'           => ['nullable', 'in:0,1'],

            // optional file updates
            'logo'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'thumbnail'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            // flags
            'remove_logo'      => ['nullable', 'in:0,1'],
            'remove_thumbnail' => ['nullable', 'in:0,1'],
        ], [
            'developer_id.required' => 'Developer id is required',
            'name.required'         => 'Name is required',
            'slug.required'         => 'Slug is required',
            'description.required'  => 'Description is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $developerId   = (int) $request->input('developer_id');
        $name          = trim($request->input('name'));
        $rawSlug       = trim($request->input('slug'));
        $email         = $request->filled('email') ? trim($request->input('email')) : null;
        $description   = (string) $request->input('description');
        $active        = (int) $request->input('active', 1);
        $removeLogo    = (int) $request->input('remove_logo', 0) === 1;
        $removeThumb   = (int) $request->input('remove_thumbnail', 0) === 1;

        // normalize slug
        $slug = Str::slug($rawSlug);
        if ($slug === '') {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => ['slug' => ['Slug is required']],
            ], 422);
        }

        $existing = DB::table('developers')->where('id', $developerId)->first();
        if (!$existing) {
            return response()->json([
                'message' => 'Developer not found',
            ], 404);
        }

        // unique slug check (ignore current row)
        $slugExists = DB::table('developers')
            ->where('slug', $slug)
            ->where('id', '!=', $developerId)
            ->exists();

        if ($slugExists) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => ['slug' => ['Slug already exists']],
            ], 422);
        }

        // prepare updates
        $update = [
            'name'        => $name,
            'slug'        => $slug,
            'email'       => $email,
            'description' => $description,
            'active'      => $active,
            'updated_at'  => now(),
        ];

        // We store files in storage/app/public/ (ROOT), and DB stores ONLY filename.
        // Public URL becomes: /storage/<filename>
        $newLogoName = null;
        $newThumbName = null;

        try {
            DB::beginTransaction();

            // --- LOGO logic ---
            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                $ext = strtolower($logoFile->getClientOriginalExtension());
                $base = Str::slug($name) ?: 'developer';
                $newLogoName = "{$base}_logo_" . time() . "_" . Str::random(6) . ".{$ext}";

                // store in public disk root
                Storage::disk('public')->putFileAs('', $logoFile, $newLogoName);

                // update db filename
                $update['logo'] = $newLogoName;

                // delete old logo file (if exists)
                if (!empty($existing->logo)) {
                    Storage::disk('public')->delete($existing->logo);
                }
            } elseif ($removeLogo) {
                // remove logo
                $update['logo'] = null;
                if (!empty($existing->logo)) {
                    Storage::disk('public')->delete($existing->logo);
                }
            }

            // --- THUMBNAIL logic ---
            if ($request->hasFile('thumbnail')) {
                $thumbFile = $request->file('thumbnail');
                $ext = strtolower($thumbFile->getClientOriginalExtension());
                $base = Str::slug($name) ?: 'developer';
                $newThumbName = "{$base}_thumb_" . time() . "_" . Str::random(6) . ".{$ext}";

                Storage::disk('public')->putFileAs('', $thumbFile, $newThumbName);

                $update['thumbnail'] = $newThumbName;

                if (!empty($existing->thumbnail)) {
                    Storage::disk('public')->delete($existing->thumbnail);
                }
            } elseif ($removeThumb) {
                $update['thumbnail'] = null;
                if (!empty($existing->thumbnail)) {
                    Storage::disk('public')->delete($existing->thumbnail);
                }
            }

            DB::table('developers')->where('id', $developerId)->update($update);

            $developer = DB::table('developers')->where('id', $developerId)->first();

            DB::commit();

            return response()->json([
                'message' => 'Developer updated successfully',
                'data'    => $developer,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            // cleanup newly uploaded files if something failed after upload
            if ($newLogoName) Storage::disk('public')->delete($newLogoName);
            if ($newThumbName) Storage::disk('public')->delete($newThumbName);

            return response()->json([
                'message' => 'Server error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function getDeveloper(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'developer_id' => ['required', 'integer', 'min:1'],
        ], [
            'developer_id.required' => 'Developer id is required',
            'developer_id.integer'  => 'Developer id must be an integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $id = (int) $request->query('developer_id');

        $developer = DB::table('developers')
            ->select([
                'id',
                'name',
                'slug',
                'email',
                'logo',              // ✅ filename only
                'thumbnail',         // ✅ filename only
                'description',
                'description_top',
                'description_bottom',
                'contact_inf',
                'active',
                'created_at',
                'updated_at',
            ])
            ->where('id', $id)
            ->first();

        if (!$developer) {
            return response()->json([
                'message' => 'Developer not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $developer,
        ], 200);
    }
   public function createDeveloper(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'        => ['required', 'string', 'max:255'],
        'slug'        => ['required', 'string', 'max:256'],
        'email'       => ['nullable', 'email', 'max:255'],
        'description' => ['required', 'string'],
        'active'      => ['nullable', 'in:0,1'],
        'logo'        => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        'thumbnail'   => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
    ], [
        'name.required'        => 'Name is required',
        'slug.required'        => 'Slug is required',
        'description.required' => 'Description is required',
        'logo.required'        => 'Logo is required',
        'thumbnail.required'   => 'Thumbnail is required',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $name        = trim($request->input('name'));
    $rawSlug     = trim($request->input('slug'));
    $email       = $request->filled('email') ? trim($request->input('email')) : null;
    $description = $request->input('description');
    $active      = (int) $request->input('active', 1);

    // ✅ Force clean slug
    $slug = Str::slug($rawSlug);
    if ($slug === '') {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => ['slug' => ['Slug is required']],
        ], 422);
    }

    // ✅ Unique check (DB)
    $slugExists = DB::table('developers')->where('slug', $slug)->exists();
    if ($slugExists) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => ['slug' => ['Slug already exists']],
        ], 422);
    }

    // ---------- Store files ----------
    $logoFile  = $request->file('logo');
    $thumbFile = $request->file('thumbnail');

    $logoExt  = $logoFile->getClientOriginalExtension();
    $thumbExt = $thumbFile->getClientOriginalExtension();

    $base = Str::slug($name) ?: 'developer';
    $ts   = time();

    $logoName  = "{$base}_logo_{$ts}." . $logoExt;
    $thumbName = "{$base}_thumb_{$ts}." . $thumbExt;

    // stored in: storage/app/public/developers/...
    $logoPath  = $logoFile->storeAs('', $logoName, 'public');
    $thumbPath = $thumbFile->storeAs('', $thumbName, 'public');


  // Save only file names in DB
$logoDbName  = $logoName;
$thumbDbName = $thumbName;


    try {
        DB::beginTransaction();

        // ✅ insert + get id
        $id = DB::table('developers')->insertGetId([
            'name'               => $name,
            'slug'               => $slug,
            'email'              => $email,
            'contact_inf'        => null,
            'logo'               => $logoDbName,
            'thumbnail'          => $thumbDbName,
            'description'        => $description,
            'description_top'    => null,
            'description_bottom' => null,
            'active'             => $active,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $developer = DB::table('developers')->where('id', $id)->first();

        DB::commit();

        return response()->json([
            'message' => 'Developer created successfully',
            'data'    => $developer,
        ], 200);
    } catch (\Throwable $e) {
        DB::rollBack();

        // ✅ cleanup uploaded files if db insert fails
        if (!empty($logoPath)) {
            Storage::disk('public')->delete($logoPath);
        }
        if (!empty($thumbPath)) {
            Storage::disk('public')->delete($thumbPath);
        }

        return response()->json([
            'message' => 'Server error',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
  public function fetchAllProjects(Request $request)
{
    $perPage = (int) $request->input('per_page', 12);
    $page = (int) $request->input('page', 1);
    $search = trim((string) $request->input('search', ''));
    // $perPage = (int) $request->input('per_page', 12);
    // $perPage = max(1, min($perPage, 50));

    // $page = (int) $request->input('page', 1);
    // $page = max(1, $page);

    // $search = trim((string) $request->input('search', ''));

    $baseQuery = DB::table('projects')
        ->where('active', 1)
        ->select(
            'id as project_id',
            'name as project_name',
            'slug as project_slug',
            'description as project_description',
            'main_image as project_main_image',
            'community_name as project_community_name',
            'starting_price as project_starting_price',
            'handover as project_handover'
        );

    // Apply search (only if provided)
    if ($search !== '') {
        $baseQuery->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('community_name', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%")
              ->orWhere('starting_price', 'like', "%{$search}%")
              ->orWhere('handover', 'like', "%{$search}%");
        });
    }

    $baseQuery->orderByDesc('id');

    // Count AFTER search filters
    $total = (clone $baseQuery)->count();

    // if ($total === 0) {
    //     return response()->json([
    //         'message' => 'Projects not found.',
    //         'projects' => [],
    //         'pagination' => [
    //             'current_page' => $page,
    //             'per_page'     => $perPage,
    //             'total'        => 0,
    //             'last_page'    => 0,
    //             'has_more'     => false,
    //         ],
    //     ], 404);
    // }

    $rows = $baseQuery
        ->forPage($page, $perPage)
        ->get();

    $projects = $rows
        ->filter(fn ($row) => !is_null($row->project_id))
        ->map(fn ($row) => [
            'id'                     => (int) $row->project_id,
            'name'                   => $row->project_name,
            'slug'                   => $row->project_slug,
            'description'            => $row->project_description,
            'project_main_image'     => $row->project_main_image,
            'project_community_name' => $row->project_community_name,
            'project_starting_price' => $row->project_starting_price,
            'project_handover'       => $row->project_handover,
        ])
        ->unique('id')
        ->values();

    $lastPage = (int) ceil($total / $perPage);

    return response()->json([
    'data' => [
        'projects' => $projects,
        'pagination' => [
            'current_page' => $page,
            'per_page'     => $perPage,
            'total'        => $total,
            'last_page'    => $lastPage,
            'has_more'     => $page < $lastPage,
        ],
    ],
    'status' => true,
    'error' => null,
    'statusCode' => 200,
]);
}

    public function show(string $slug)
    {
        $rows = DB::table('developers')
            ->leftJoin('communities', 'communities.developer_id', '=', 'developers.id')
            ->where('developers.slug', $slug)
            ->select(
                'developers.id as developer_id',
                'developers.name as developer_name',
                'developers.description as developer_description',
                'developers.slug as developer_slug',
                'developers.logo as developer_logo',
                'developers.thumbnail as developer_main_image',
                'communities.id as community_id',
                'communities.name as community_name',
                'communities.slug as community_slug',
                'communities.description as community_description',
                'communities.main_image as community_main_image',
                'communities.selling_point as community_selling_point',
            )
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'message' => 'Developer not found.',
            ], 404);
        }

        // First row = developer info
        $developerRow = $rows->first();

        $communities = $rows
            ->filter(fn ($row) => !is_null($row->community_id))
            ->map(fn ($row) => [
                'id'          => $row->community_id,
                'name'        => $row->community_name,
                'slug'        => $row->community_slug,
                'description' => $row->community_description,
                'community_main_image' => $row->community_main_image,
                'selling_point' => $row->community_selling_point,
            ])
            ->values()
            ->all();

        return response()->json([
            'developer' => [
                'id'          => $developerRow->developer_id,
                'name'        => $developerRow->developer_name,
                'slug'        => $developerRow->developer_slug,
                'logo'        => $developerRow->developer_logo,
                'description' => $developerRow->developer_description,
                'developer_main_image' => $developerRow->developer_main_image,
            ],
            'communities' => $communities,
        ]);
    }

    public function projectsFromDeveloper(string $slug)
    {
       $rows = DB::table('developers')
        // ->leftJoin('communities', 'communities.developer_id', '=', 'developers.id')
        ->leftJoin('projects', 'projects.developer_id', '=', 'developers.id')
        ->where('developers.slug', $slug)
        ->select(
            'developers.id as developer_id',
            'developers.name as developer_name',
            'developers.description as developer_description',
            'developers.slug as developer_slug',
            'developers.logo as developer_logo',
            'developers.thumbnail as developer_main_image',

            // 'communities.id as community_id',
            // 'communities.name as community_name',
            // 'communities.slug as community_slug',
            // 'communities.description as community_description',
            // 'communities.main_image as community_main_image',
            // 'communities.selling_point as community_selling_point',

            'projects.id as project_id',
            'projects.name as project_name',
            'projects.slug as project_slug',
            'projects.description as project_description',
            'projects.main_image as project_main_image',
            'projects.community_name as project_community_name',
            'projects.starting_price as project_starting_price',
            'projects.handover as project_handover',
        )
        ->get();

    if ($rows->isEmpty()) {
        return response()->json([
            'message' => 'Developer not found.',
        ], 404);
    }

    $developerRow = $rows->first();
    // $communities = $rows
    //     ->filter(fn ($row) => !is_null($row->community_id))
    //     ->map(fn ($row) => [
    //         'id'                   => $row->community_id,
    //         'name'                 => $row->community_name,
    //         'slug'                 => $row->community_slug,
    //         'description'          => $row->community_description,
    //         'community_main_image' => $row->community_main_image,
    //         'selling_point'        => $row->community_selling_point,
    //     ])
    //     ->unique('id')
    //     ->values()
    //     ->all();

    $projects = $rows
        ->filter(fn ($row) => !is_null($row->project_id))
        ->map(fn ($row) => [
            'id'                 => $row->project_id,
            'name'               => $row->project_name,
            'slug'               => $row->project_slug,
            'description'        => $row->project_description,
            'project_main_image' => $row->project_main_image,
            'project_community_name' => $row->project_community_name,
            'project_starting_price' => $row->project_starting_price,
            'project_handover'   => $row->project_handover,
            // 'community_id'       => $row->community_id,
            // 'community_name'     => $row->community_name,
            // 'community_slug'     => $row->community_slug,
        ])
        ->unique('id')
        ->values()
        ->all();

    return response()->json([
        'developer' => [
            'id'                  => $developerRow->developer_id,
            'name'                => $developerRow->developer_name,
            'slug'                => $developerRow->developer_slug,
            'logo'                => $developerRow->developer_logo,
            'description'         => $developerRow->developer_description,
            'developer_main_image'=> $developerRow->developer_main_image,
        ],
        // 'communities' => $communities,
        'projects'    => $projects,   
    ]);
    }

    public function projectsFromCommunity(Request $request)
{
    $community_name = trim((string) $request->input('community_name', ''));
    // $community_name = trim((string) $request->input('community_name', ''));

    if ($community_name === '') {
        return response()->json([
            'success' => false,
            'message' => 'Community name is required.',
            'data' => null,
        ], 422);
    }

    $rows = DB::table('communities')
        ->leftJoin('projects', 'projects.community_id', '=', 'communities.id')
        ->where('communities.name', $community_name)
        ->select(
            // community
            'communities.id as community_id',
            'communities.name as community_name',
            'communities.slug as community_slug',
            'communities.description as community_description',
            'communities.main_image as community_main_image',
            'communities.selling_point as community_selling_point',
            'communities.about as community_about',

            // projects
            'projects.id as project_id',
            'projects.name as project_name',
            'projects.slug as project_slug',
            'projects.description as project_description',
            'projects.main_image as project_main_image',
            'projects.community_name as project_community_name',
            'projects.starting_price as project_starting_price',
            'projects.handover as project_handover'
        )
        ->get();

    if ($rows->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Community not found.',
            'data' => null,
        ], 404);
    }

    $communityRow = $rows->first();

    // ✅ Projects
    $projects = $rows
        ->filter(fn ($row) => !is_null($row->project_id))
        ->map(fn ($row) => [
            'id'                     => $row->project_id,
            'name'                   => $row->project_name,
            'slug'                   => $row->project_slug,
            'description'            => $row->project_description,
            'project_main_image'     => $row->project_main_image,
            'project_community_name' => $row->project_community_name,
            'project_starting_price' => $row->project_starting_price,
            'project_handover'       => $row->project_handover,
        ])
        ->unique('id')
        ->values()
        ->all();

    // ✅ FAQs (from faq_communities)
    $faqs = DB::table('faq_communities')
        ->where('community_id', $communityRow->community_id)
        ->where('active', 1)
        ->orderBy('id', 'asc')
        ->get(['id', 'title', 'description'])
        ->map(fn ($faq) => [
            'id'       => $faq->id,
            'question' => $faq->title,
            'answer'   => $faq->description, // contains HTML (<p>...</p><br/> etc.)
        ])
        ->values()
        ->all();

    return response()->json([
        'success' => true,
        'data' => [
            'community' => [
                'id'            => $communityRow->community_id,
                'name'          => $communityRow->community_name,
                'slug'          => $communityRow->community_slug,
                'description'   => $communityRow->community_description,
                'main_image'    => $communityRow->community_main_image,
                'selling_point' => $communityRow->community_selling_point,
                'about'         => $communityRow->community_about,
            ],
            'projects' => $projects,
            'faqs'     => $faqs, // ✅ added
        ],
    ], 200);
}

   public function fetchDevelopers(Request $request)
{
    $perPage = (int) $request->query('per_page', 10);
    $perPage = max(1, min($perPage, 100));

    $page = (int) $request->query('page', 1);
    $search = trim($request->query('search', ''));

    $query = DB::table('developers')
        ->select([
            'id',
            'name',
            'email',
            'logo',
            'thumbnail',
            'active',
            'created_at',
            'updated_at',
        ]);

    // 🔍 Search Support (first_name + last_name)
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%");
        });
    }

    $users = $query
        ->orderByDesc('id')
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json([
        'success' => true,
        'data' => $users->items(),
        'pagination' => [
            'current_page' => $users->currentPage(),
            'last_page'    => $users->lastPage(),
            'per_page'     => $users->perPage(),
            'total'        => $users->total(),
        ]
    ]);
}

 public function changeStatusDeveloper(Request $request)
{
      $validator = Validator::make($request->all(), [
        'developer_id' => 'required|integer|exists:developers,id',
        'active'  => 'required|in:0,1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    DB::table('developers')
        ->where('id', $request->developer_id)
        ->update([
            'active' => $request->active,
            'updated_at' => now(),
        ]);

    return response()->json([
        'message' => 'Developer status updated successfully',
    ]);
}

}
