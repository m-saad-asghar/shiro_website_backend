<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectController extends Controller
{


     public function fetchAllProjects(Request $request)
{
    $perPage = (int) $request->query('per_page', 10);
    $perPage = max(1, min($perPage, 100));

    $page = (int) $request->query('page', 1);
    $search = trim($request->query('search', ''));

    $query = DB::table('projects')
        ->select([
            'id',
            'name',
            'main_image',
            'community_name',
            'starting_price',
            'handover',
            'payment_plan',
            'active',
            'created_at',
        ]);

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%");
        });
    }

    $projects = $query
        ->orderByDesc('id')
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json([
        'success' => true,
        'data' => $projects->items(),
        'pagination' => [
            'current_page' => $projects->currentPage(),
            'last_page'    => $projects->lastPage(),
            'per_page'     => $projects->perPage(),
            'total'        => $projects->total(),
        ]
    ]);
}
 public function show(string $slug)
{
    $rows = DB::table('projects')
        // ->join('communities', 'communities.id', '=', 'projects.community_id')
        ->join('developers', 'developers.id', '=', 'projects.developer_id')
        ->where('projects.slug', $slug)
        ->select(
            'projects.id as project_id',
            'projects.name as project_name',
            'projects.description as project_description',
            'projects.slug as project_slug',
            'projects.main_image as project_main_image',
            'projects.starting_price as starting_price',
            'projects.handover as handover',
            'projects.payment_plan as payment_plan',
            'projects.payment_plan_description as payment_plan_description',
            // 'communities.name as community_name',
            'developers.name as developer_name',
        )
        ->get();

    if ($rows->isEmpty()) {
        return response()->json([
            'message' => 'Project not found.',
        ], 404);
    }

    $projectIds = $rows->pluck('project_id')->unique()->values();

    /** ✅ Images */
    $imagesByProject = DB::table('image_projects')
        ->whereIn('project_id', $projectIds)
        ->select('project_id', 'image')
        ->get()
        ->groupBy('project_id')
        ->map(fn ($items) => $items->pluck('image')->values());

    /** ✅ Amenities */
    $amenitiesByProject = DB::table('amenity_projects')
        ->join('amenities', 'amenities.id', '=', 'amenity_projects.amenity_id')
        ->whereIn('amenity_projects.project_id', $projectIds)
        ->select('amenity_projects.project_id', 'amenities.name as amenity_name')
        ->get()
        ->groupBy('project_id')
        ->map(fn ($items) => $items->pluck('amenity_name')->values());

    /** ✅ Payment Plans */
    $paymentPlansByProject = DB::table('paymentplan_projects')
        ->whereIn('project_id', $projectIds)
        ->where('active', 1)
        ->select('project_id', 'title', 'value', 'sub_title')
        ->orderBy('id', 'asc')
        ->get()
        ->groupBy('project_id')
        ->map(fn ($items) => $items->map(fn ($p) => [
            'title' => $p->title,
            'value' => $p->value,
            'sub_title' => $p->sub_title,
        ])->values());

    /** ✅ FAQs */
    $faqsByProject = DB::table('faq_projects')
        ->whereIn('project_id', $projectIds)
        ->where('active', 1)
        ->select('project_id', 'id', 'title', 'description')
        ->orderBy('id', 'asc')
        ->get()
        ->groupBy('project_id')
        ->map(fn ($items) => $items->map(fn ($f) => [
            'id'       => $f->id,
            'question' => $f->title,
            'answer'   => $f->description,
        ])->values());

    /** ✅ Floorplans (NEW) */
    $floorplansByProject = DB::table('floorplan_projects')
        ->whereIn('project_id', $projectIds)
        ->where('active', 1) // remove if you want inactive too
        ->select('project_id', 'title', 'image')
        ->orderBy('id', 'asc')
        ->get()
        ->groupBy('project_id')
        ->map(fn ($items) => $items->map(fn ($fp) => [
            'title' => $fp->title,
            'value' => $fp->image, // keep same structure as payment_plans for frontend component
        ])->values());

        /** ✅ Unique Selling Points (NEW) */
$uniqueSellingPointsByProject = DB::table('project_unique_selling_points')
    ->whereIn('project_id', $projectIds)
    // ->where('active', 1) // if you have active column
    ->select('project_id', 'id', 'title', 'description', 'main_image')
    ->orderBy('id', 'asc')
    ->get()
    ->groupBy('project_id')
    ->map(fn ($items) => $items->map(fn ($usp) => [
        'id' => $usp->id,
        'title' => $usp->title,
        'description' => $usp->description,
        'main_image' => $usp->main_image,
    ])->values());

    /** ✅ Location*/
$locationOfProject = DB::table('project_locations')
    ->whereIn('project_id', $projectIds)
    // ->where('active', 1) // if you have active column
    ->select('project_id', 'id', 'title', 'description', 'main_image', 'map_link')
    ->orderBy('id', 'asc')
    ->get()
    ->groupBy('project_id')
    ->map(fn ($items) => $items->map(fn ($usp) => [
        'id' => $usp->id,
        'title' => $usp->title,
        'description' => $usp->description,
        'main_image' => $usp->main_image,
        'map_link' => $usp->map_link,
    ])->values());

    /** ✅ Attach all arrays */
    $rows = $rows->map(function ($row) use (
        $imagesByProject,
        $amenitiesByProject,
        $paymentPlansByProject,
        $faqsByProject,
        $floorplansByProject,
        $uniqueSellingPointsByProject,
        $locationOfProject,
    ) {
        $row->images = $imagesByProject[$row->project_id] ?? collect([]);
        $row->amenities = $amenitiesByProject[$row->project_id] ?? collect([]);
        $row->payment_plans = $paymentPlansByProject[$row->project_id] ?? collect([]);
        $row->faqs = $faqsByProject[$row->project_id] ?? collect([]);
        $row->floorplans = $floorplansByProject[$row->project_id] ?? collect([]);
        $row->unique_selling_points = $uniqueSellingPointsByProject[$row->project_id] ?? collect([]);
        $row->location = $locationOfProject[$row->project_id] ?? collect([]);
        return $row;
    });

    $projects = $rows
        ->filter(fn ($row) => !is_null($row->project_id))
        ->map(fn ($row) => [
            'id'          => $row->project_id,
            'name'        => $row->project_name,
            'slug'        => $row->project_slug,
            'description' => $row->project_description,
            'project_main_image' => $row->project_main_image,
            'project_starting_price' => $row->starting_price,
            'project_handover' => $row->handover,
            'project_payment_plan' => $row->payment_plan,
            'project_payment_plan_description' => $row->payment_plan_description,
            // 'community_name' => $row->community_name,
            'developer_name' => $row->developer_name,

            // ✅ arrays
            'images' => $row->images ?? [],
            'amenities' => $row->amenities ?? [],
            'payment_plans' => $row->payment_plans ?? [],
            'faqs' => $row->faqs ?? [],
            'floorplans' => $row->floorplans ?? [],
            'unique_selling_points' => $row->unique_selling_points ?? [],
            'location' => $row->location ?? [],
        ])
        ->values()
        ->first();

    return response()->json([
        'projects' => $projects,
    ]);
}

public function downloadBrochure(Request $request)
{
    // Validate GET query param
    $request->validate([
        'project_id' => 'required|integer'
    ]);

    $projectId = (int) $request->query('project_id');

    // Fetch project
    $project = DB::table('projects')
        ->select('brochure')
        ->where('id', $projectId)
        ->first();

    if (!$project || empty($project->brochure)) {
        return response()->json([
            'status' => 0,
            'message' => 'Brochure not found.'
        ], 404);
    }

    // Secure filename
    $fileName = basename($project->brochure);
    $filePath = storage_path('app/public/projects/' . $fileName);

    if (!file_exists($filePath)) {
        return response()->json([
            'status' => 0,
            'message' => 'Brochure file missing on server.'
        ], 404);
    }

    return response()->download($filePath, $fileName);
}

 public function editData(Request $request, int $project_id)
    {
        try {
            // 1) Main project
            $project = DB::table('projects')
                ->where('id', $project_id)
                ->first();

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found.',
                ], 404);
            }

            // 2) Amenities (pivot: amenity_projects)
            // If you have an `amenities` table, you can join it. For now, returning amenity_ids.
            $amenities = DB::table('amenity_projects')
                ->where('project_id', $project_id)
                ->orderBy('id', 'asc')
                ->get(['id', 'amenity_id', 'project_id', 'created_at', 'updated_at']);

            // 3) FAQs (faq_projects)
            $faqs = DB::table('faq_projects')
                ->where('project_id', $project_id)
                ->orderBy('id', 'asc')
                ->get(['id', 'title', 'description', 'project_id', 'active', 'created_at', 'updated_at']);

            // 4) Floorplans (floorplan_projects)
            $floorplans = DB::table('floorplan_projects')
                ->where('project_id', $project_id)
                ->orderBy('id', 'asc')
                ->get(['id', 'title', 'image', 'project_id', 'active', 'created_at', 'updated_at']);

            // 5) Unique Selling Points (project_unique_selling_points)
            $usps = DB::table('project_unique_selling_points')
                ->where('project_id', $project_id)
                ->orderBy('id', 'asc')
                ->get(['id', 'title', 'description', 'main_image', 'project_id', 'created_at', 'updated_at']);

            // 6) Images (image_projects)
            $images = DB::table('image_projects')
                ->where('project_id', $project_id)
                ->orderBy('id', 'asc')
                ->get(['id', 'image', 'project_id', 'active', 'created_at', 'updated_at']);

            // 7) Payment plans (paymentplan_projects)
            $paymentPlans = DB::table('paymentplan_projects')
                ->where('project_id', $project_id)
                ->orderBy('id', 'asc')
                ->get(['id', 'title', 'value', 'sub_title', 'active', 'project_id', 'created_at', 'updated_at']);

            // 8) Locations (project_locations)
            $locations = DB::table('project_locations')
                ->where('project_id', $project_id)
                ->orderBy('id', 'asc')
                ->get(['id', 'title', 'description', 'main_image', 'project_id', 'map_link', 'created_at', 'updated_at']);

            return response()->json([
                'success' => true,
                'data' => [
                    'project' => $project,
                    'amenities' => $amenities,
                    'faqs' => $faqs,
                    'floorplans' => $floorplans,
                    'unique_selling_points' => $usps,
                    'images' => $images,
                    'payment_plans' => $paymentPlans,
                    'locations' => $locations,
                ],
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error while fetching project edit data.',
                'error' => $e->getMessage(), // remove in production if you want
            ], 500);
        }
    }

     public function amenitiesIndex()
    {
        $rows = DB::table('amenities')
            ->select('id', 'name')
            ->orderBy('name')
            ->where('active', 1)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'amenities' => $rows,
            ],
        ]);
    }

     public function fetchCommunities()
    {
        $rows = DB::table('communities')
            ->select('id', 'name')
            ->orderBy('name')
            ->where('active', 1)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'communities' => $rows,
            ],
        ]);
    }


     public function fetchDevelopersDropdown()
    {
        $rows = DB::table('developers')
            ->select('id', 'name')
            ->orderBy('name')
            ->where('active', 1)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'developers' => $rows,
            ],
        ]);
    }


  public function saveProject(Request $request)
{
    $now = now();

    // =========================
    // 1) READ PAYLOAD (JSON STRING)
    // =========================
    $payloadStr = $request->input('payload');
    $payload = is_string($payloadStr) && $payloadStr !== '' ? json_decode($payloadStr, true) : null;

    if (!is_array($payload)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid payload (expected JSON string in form-data field: payload).',
        ], 422);
    }

    $pget = function (string $key, $default = null) use ($payload) {
        return data_get($payload, $key, $default);
    };

    // =========================
    // HELPERS
    // =========================
    $safeFileBase = function (string $name): string {
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = preg_replace('/[^A-Za-z0-9\-\s]/', '', $name);
        return trim($name) ?: 'Project';
    };

  
  $storeToPublic = function ($file, string $dir, string $filenameWithExt) {
    $file->storeAs(trim($dir, '/'), $filenameWithExt, 'public');
    return $filenameWithExt; // ✅ only filename in DB
};

    $asArray = function ($value): array {
        return is_array($value) ? $value : [];
    };

    $asIntArray = function ($value): array {
        if (!is_array($value)) return [];
        $out = array_map(fn($v) => (int)$v, $value);
        return array_values(array_filter($out, fn($v) => $v > 0));
    };

    // =========================
    // 2) BASIC VALIDATION
    // =========================
    $projectName = trim((string)$pget('project.name', ''));
    $slug        = trim((string)$pget('project.slug', ''));
    $communityId = (int)$pget('project.community_id', 0);
    $developerId = (int)$pget('project.developer_id', 0);

    if ($projectName === '' || $slug === '' || $communityId <= 0 || $developerId <= 0) {
        return response()->json([
            'success' => false,
            'message' => 'Missing required fields: project.name, project.slug, project.community_id, project.developer_id',
        ], 422);
    }

    // =========================
    // 3) FILES (FORM-DATA KEYS)
    // =========================
    $mainImageFile = $request->file('project_main_image');
    $brochureFile  = $request->file('brochure');
    $uspImageFile  = $request->file('usp_main_image');

    $floorplanFiles = $request->file('floorplan_images', []);
    $galleryFiles   = $request->file('gallery_images', []);

    if (!is_array($floorplanFiles)) $floorplanFiles = [];
    if (!is_array($galleryFiles))   $galleryFiles   = [];

    // ✅ location_images removed

    // =========================
    // 4) ARRAYS FROM PAYLOAD JSON
    // =========================
    $amenityIds   = $asIntArray($pget('amenity_ids', []));
    $faqs         = $asArray($pget('faqs', []));
    $floorplans   = $asArray($pget('floorplans', []));
    $gallery      = $asArray($pget('images', []));
    $paymentPlans = $asArray($pget('payment_plans', []));
    $locations    = $asArray($pget('locations', [])); // expects [] or [ {title, description, map_link} ]
    $uspArr       = $asArray($pget('unique_selling_points', []));
    $uspRow       = (count($uspArr) > 0 && is_array($uspArr[0])) ? $uspArr[0] : null;

    // ✅ only one location allowed (frontend sends max 1 anyway)
    $locations = array_slice($locations, 0, 1);

    try {
        $projectId = DB::transaction(function () use (
            $payload, $pget, $now,
            $projectName, $slug, $communityId, $developerId,
            $safeFileBase, $storeToPublic,
            $mainImageFile, $brochureFile, $uspImageFile,
            $amenityIds, $faqs, $floorplans, $gallery, $paymentPlans, $locations, $uspRow,
            $floorplanFiles, $galleryFiles
        ) {
            $safeProjectName = $safeFileBase($projectName);

            // 1) project main image
            $mainImagePath = null;
            if ($mainImageFile) {
                $ext = strtolower($mainImageFile->getClientOriginalExtension() ?: 'jpg');
                $name = Str::slug($slug) . '-' . time() . '.' . $ext;
                $mainImagePath = $storeToPublic($mainImageFile, '', $name);
            }

            // 2) brochure
            $brochurePath = null;
            if ($brochureFile) {
                $ext = strtolower($brochureFile->getClientOriginalExtension() ?: 'pdf');
                $base = "Shiro Estate {$safeProjectName} Brochure";
                $name = $base . '-' . time() . '.' . $ext;
                $brochurePath = $storeToPublic($brochureFile, 'projects', $name);
            }

            // 3) insert project
            $projectData = [
                'name' => $projectName,
                'slug' => $slug,
                'description' => data_get($payload, 'project.description'),
                'main_image' => $mainImagePath,
                'brochure' => $brochurePath,

                'community_id' => $communityId,
                'community_name' => (string) data_get($payload, 'project.community_name', ''),

                'developer_id' => $developerId,
                'starting_price' => data_get($payload, 'project.starting_price'),
                'handover' => data_get($payload, 'project.handover'),
                'payment_plan' => data_get($payload, 'project.payment_plan'),
                'payment_plan_description' => data_get($payload, 'project.payment_plan_description'),
                'active' => (int) data_get($payload, 'project.active', 1),

                'created_at' => $now,
                'updated_at' => $now,
            ];

            $projectId = DB::table('projects')->insertGetId($projectData);

            // 4) amenity_projects
            if (!empty($amenityIds)) {
                $rows = [];
                foreach ($amenityIds as $aid) {
                    $rows[] = [
                        'amenity_id' => $aid,
                        'project_id' => $projectId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                DB::table('amenity_projects')->insert($rows);
            }

            // 5) faq_projects
            if (!empty($faqs)) {
                $rows = [];
                foreach ($faqs as $f) {
                    $rows[] = [
                        'title' => (string)($f['title'] ?? ''),
                        'description' => (string)($f['description'] ?? ''),
                        'project_id' => $projectId,
                        'active' => (int)($f['active'] ?? 1),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                DB::table('faq_projects')->insert($rows);
            }

            // 6) floorplan_projects + save floorplan_images[i]
            if (!empty($floorplans)) {
                $rows = [];
                foreach ($floorplans as $i => $fp) {
                    $imgPath = null;

                    if (isset($floorplanFiles[$i]) && $floorplanFiles[$i]) {
                        $file = $floorplanFiles[$i];
                        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                        $name = 'floorplan-' . $projectId . '-' . ($i + 1) . '-' . time() . '.' . $ext;
                        $imgPath = $storeToPublic($file, 'projects', $name);
                    } else {
                        $imgPath = $fp['image'] ?? null; // fallback
                    }

                    $rows[] = [
                        'title' => (string)($fp['title'] ?? ''),
                        'image' => (string)($imgPath ?? ''),
                        'project_id' => $projectId,
                        'active' => (int)($fp['active'] ?? 1),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                DB::table('floorplan_projects')->insert($rows);
            }

            // 7) image_projects (gallery) + save gallery_images[i]
            if (!empty($gallery)) {
                $rows = [];
                foreach ($gallery as $i => $im) {
                    $imgPath = null;

                    if (isset($galleryFiles[$i]) && $galleryFiles[$i]) {
                        $file = $galleryFiles[$i];
                        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                        $name = 'gallery-' . $projectId . '-' . ($i + 1) . '-' . time() . '.' . $ext;
                        $imgPath = $storeToPublic($file, 'projects', $name);
                    } else {
                        $imgPath = $im['image'] ?? null;
                    }

                    $rows[] = [
                        'image' => (string)($imgPath ?? ''),
                        'project_id' => $projectId,
                        'active' => (int)($im['active'] ?? 1),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                DB::table('image_projects')->insert($rows);
            }

            // 8) paymentplan_projects
            if (!empty($paymentPlans)) {
                $rows = [];
                foreach ($paymentPlans as $pp) {
                    $rows[] = [
                        'title' => (string)($pp['title'] ?? ''),
                        'value' => (string)($pp['value'] ?? ''),
                        'sub_title' => $pp['sub_title'] ?? null,
                        'active' => (int)($pp['active'] ?? 1),
                        'project_id' => $projectId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                DB::table('paymentplan_projects')->insert($rows);
            }

            // 9) project_locations (SINGLE, NO IMAGE)
            if (!empty($locations)) {
                $loc = $locations[0];

                DB::table('project_locations')->insert([
                    'title' => (string)($loc['title'] ?? ''),
                    'description' => (string)($loc['description'] ?? ''),
                    'project_id' => $projectId,
                    'map_link' => $loc['map_link'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // 10) USP (single) + save usp_main_image
            if (is_array($uspRow) && trim((string)($uspRow['title'] ?? '')) !== '') {
                $uspImagePath = null;

                if ($uspImageFile) {
                    $ext = strtolower($uspImageFile->getClientOriginalExtension() ?: 'jpg');
                    $name = 'usp-' . $projectId . '-' . time() . '.' . $ext;
                    $uspImagePath = $storeToPublic($uspImageFile, 'projects', $name);
                } else {
                    $uspImagePath = $uspRow['main_image'] ?? null;
                }

                DB::table('project_unique_selling_points')->insert([
                    'title' => (string)($uspRow['title'] ?? ''),
                    'description' => (string)($uspRow['description'] ?? ''),
                    'main_image' => (string)($uspImagePath ?? ''),
                    'project_id' => $projectId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return $projectId;
        });

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => ['project_id' => $projectId],
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Create failed: ' . $e->getMessage(),
        ], 500);
    }
}

public function changeStatusProject(Request $request)
{
    $validator = Validator::make($request->all(), [
        'project_id' => ['required', 'integer', 'exists:projects,id'],
        'active'     => ['required', 'integer', 'in:0,1'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $projectId = (int) $request->input('project_id');
    $active    = (int) $request->input('active');

    try {

        $updated = DB::table('projects')
            ->where('id', $projectId)
            ->update([
                'active'     => $active,
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            return response()->json([
                'success' => true,
                'message' => 'No change (already same status)',
                'data'    => [
                    'project_id' => $projectId,
                    'active'     => $active,
                ],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Project status updated successfully',
            'data'    => [
                'project_id' => $projectId,
                'active'     => $active,
            ],
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Server error while updating project status',
            'error'   => $e->getMessage(),
        ], 500);
    }
}



public function updateProject(Request $request, $id)
{
    $now = now();

    // =========================
    // 1) READ PAYLOAD (JSON STRING)
    // =========================
    $payloadStr = $request->input('payload');
    $payload = is_string($payloadStr) && $payloadStr !== '' ? json_decode($payloadStr, true) : null;

    if (!is_array($payload)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid payload (expected JSON string in form-data field: payload).',
        ], 422);
    }

    $pget = function (string $key, $default = null) use ($payload) {
        return data_get($payload, $key, $default);
    };

    // =========================
    // HELPERS
    // =========================
    $safeFileBase = function (string $name): string {
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = preg_replace('/[^A-Za-z0-9\-\s]/', '', $name);
        return trim($name) ?: 'Project';
    };

    $storeToPublic = function ($file, string $dir, string $filenameWithExt) {
        $file->storeAs(trim($dir, '/'), $filenameWithExt, 'public');
        return $filenameWithExt; // ✅ only filename in DB
    };

    $toInt = fn($v) => is_numeric($v) ? (int)$v : 0;
    $toStr = fn($v) => is_null($v) ? null : (string)$v;

    // =========================
    // 2) BASIC VALIDATION
    // =========================
    $project = $pget('project', []);
    if (!is_array($project)) $project = [];

    $name        = trim((string)($project['name'] ?? ''));
    $slug        = trim((string)($project['slug'] ?? ''));
    $communityId = $toInt($project['community_id'] ?? 0);
    $developerId = $toInt($project['developer_id'] ?? 0);
    $active      = $toInt($project['active'] ?? 0);

    if ($name === '' || $slug === '') {
        return response()->json(['success' => false, 'message' => 'Name and slug are required.'], 422);
    }
    if ($communityId <= 0) {
        return response()->json(['success' => false, 'message' => 'Community is required.'], 422);
    }
    if ($developerId <= 0) {
        return response()->json(['success' => false, 'message' => 'Developer is required.'], 422);
    }
    if (!in_array($active, [0, 1], true)) {
        return response()->json(['success' => false, 'message' => 'Active must be 0 or 1.'], 422);
    }

    // =========================
    // 3) FETCH EXISTING PROJECT
    // =========================
    $existing = DB::table('projects')->where('id', (int)$id)->first();
    if (!$existing) {
        return response()->json(['success' => false, 'message' => 'Project not found.'], 404);
    }

    // =========================
    // 4) FILES (OPTIONAL REPLACE / REMOVE)
    // =========================
    $base = Str::slug($safeFileBase($name));

    // MAIN IMAGE
    $mainImageFilename = $existing->main_image ?? null;
    if ($request->hasFile('project_main_image')) {
        $f = $request->file('project_main_image');
        $ext = strtolower($f->getClientOriginalExtension() ?: 'jpg');
        $mainImageFilename = $storeToPublic($f, '', "{$base}-main-{$now->timestamp}.{$ext}");
    } else {
        $incoming = $project['main_image'] ?? null;
        if ($incoming === '' || $incoming === null) $mainImageFilename = null;
    }

    // BROCHURE
    $brochureFilename = $existing->brochure ?? null;
    if ($request->hasFile('brochure')) {
        $f = $request->file('brochure');
        $ext = strtolower($f->getClientOriginalExtension() ?: 'pdf');
       $brochureFilename = $storeToPublic($f, 'projects', "{$base}-brochure-{$now->timestamp}.{$ext}");
    } else {
        $incoming = $project['brochure'] ?? null;
        if ($incoming === '' || $incoming === null) $brochureFilename = null;
    }

    // USP MAIN IMAGE (upload once)
    $uspUploadedFilename = null;
    if ($request->hasFile('usp_main_image')) {
        $f = $request->file('usp_main_image');
        $ext = strtolower($f->getClientOriginalExtension() ?: 'jpg');
       $uspUploadedFilename = $storeToPublic($f, 'projects', "{$base}-usp-{$now->timestamp}.{$ext}");
    }

    // Arrays of uploaded files
    $floorplanUploads = $request->file('floorplan_images', []);
    $galleryUploads   = $request->file('gallery_images', []);

    // =========================
    // 5) TRANSACTION UPDATE
    // =========================
    DB::beginTransaction();

    try {
        // ---------- Update projects ----------
        DB::table('projects')
            ->where('id', (int)$id)
            ->update([
                'name' => $name,
                'slug' => $slug,
                'description' => $toStr($project['description'] ?? null),
                'main_image' => $mainImageFilename,
                'brochure' => $brochureFilename,
                'community_id' => $communityId,
                'developer_id' => $developerId,
                'starting_price' => $toStr($project['starting_price'] ?? null),
                'handover' => $toStr($project['handover'] ?? null),
                'payment_plan' => $toStr($project['payment_plan'] ?? null),
                'payment_plan_description' => $toStr($project['payment_plan_description'] ?? null),
                'active' => $active,
                'updated_at' => $now,
            ]);

        // ---------- Amenities (pivot) ----------
        $amenityIds = $pget('amenity_ids', []);
        if (!is_array($amenityIds)) $amenityIds = [];
        $amenityIds = array_values(array_unique(array_filter(array_map('intval', $amenityIds), fn($x) => $x > 0)));

        DB::table('amenity_projects')->where('project_id', (int)$id)->delete();
        foreach ($amenityIds as $aid) {
            DB::table('amenity_projects')->insert([
                'project_id' => (int)$id,
                'amenity_id' => (int)$aid,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ---------- FAQs (✅ faq_projects) ----------
        $faqs = $pget('faqs', []);
        if (!is_array($faqs)) $faqs = [];
        $keepFaqIds = [];

        foreach ($faqs as $row) {
            if (!is_array($row)) continue;
            $rid = isset($row['id']) ? (int)$row['id'] : 0;

            $data = [
                'title' => trim((string)($row['title'] ?? '')),
                'description' => trim((string)($row['description'] ?? '')),
                'project_id' => (int)$id,
                'active' => 1,
                'updated_at' => $now,
            ];

            if ($rid > 0) {
                DB::table('faq_projects')->where('id', $rid)->where('project_id', (int)$id)->update($data);
                $keepFaqIds[] = $rid;
            } else {
                $data['created_at'] = $now;
                $newId = DB::table('faq_projects')->insertGetId($data);
                $keepFaqIds[] = (int)$newId;
            }
        }

        DB::table('faq_projects')
            ->where('project_id', (int)$id)
            ->when(count($keepFaqIds) > 0, fn($q) => $q->whereNotIn('id', $keepFaqIds))
            ->delete();

        // ---------- Floorplans (✅ floorplan_projects) ----------
        $floorplans = $pget('floorplans', []);
        if (!is_array($floorplans)) $floorplans = [];
        $keepFloorplanIds = [];

        foreach ($floorplans as $idx => $row) {
            if (!is_array($row)) continue;

            $rid = isset($row['id']) ? (int)$row['id'] : 0;
            $title = trim((string)($row['title'] ?? ''));

            $imageFilename = trim((string)($row['image'] ?? ''));

            if (isset($floorplanUploads[$idx]) && $floorplanUploads[$idx]) {
                $f = $floorplanUploads[$idx];
                $ext = strtolower($f->getClientOriginalExtension() ?: 'jpg');
                $imageFilename = $storeToPublic($f, 'projects', "{$base}-floorplan-{$idx}-{$now->timestamp}.{$ext}");
            }

            $data = [
                'title' => $title,
                'image' => $imageFilename,
                'project_id' => (int)$id,
                'active' => 1,
                'updated_at' => $now,
            ];

            if ($rid > 0) {
                DB::table('floorplan_projects')->where('id', $rid)->where('project_id', (int)$id)->update($data);
                $keepFloorplanIds[] = $rid;
            } else {
                $data['created_at'] = $now;
                $newId = DB::table('floorplan_projects')->insertGetId($data);
                $keepFloorplanIds[] = (int)$newId;
            }
        }

        DB::table('floorplan_projects')
            ->where('project_id', (int)$id)
            ->when(count($keepFloorplanIds) > 0, fn($q) => $q->whereNotIn('id', $keepFloorplanIds))
            ->delete();

        // ---------- Gallery Images (✅ image_projects) ----------
        $images = $pget('images', []);
        if (!is_array($images)) $images = [];
        $keepImageIds = [];

        foreach ($images as $idx => $row) {
            if (!is_array($row)) continue;

            $rid = isset($row['id']) ? (int)$row['id'] : 0;
            $imageFilename = trim((string)($row['image'] ?? ''));

            if (isset($galleryUploads[$idx]) && $galleryUploads[$idx]) {
                $f = $galleryUploads[$idx];
                $ext = strtolower($f->getClientOriginalExtension() ?: 'jpg');
                $imageFilename = $storeToPublic($f, 'projects', "{$base}-gallery-{$idx}-{$now->timestamp}.{$ext}");
            }

            $data = [
                'image' => $imageFilename,
                'project_id' => (int)$id,
                'active' => 1,
                'updated_at' => $now,
            ];

            if ($rid > 0) {
                DB::table('image_projects')->where('id', $rid)->where('project_id', (int)$id)->update($data);
                $keepImageIds[] = $rid;
            } else {
                $data['created_at'] = $now;
                $newId = DB::table('image_projects')->insertGetId($data);
                $keepImageIds[] = (int)$newId;
            }
        }

        DB::table('image_projects')
            ->where('project_id', (int)$id)
            ->when(count($keepImageIds) > 0, fn($q) => $q->whereNotIn('id', $keepImageIds))
            ->delete();

        // ---------- Payment Plans (✅ paymentplan_projects) ----------
        $paymentPlans = $pget('payment_plans', []);
        if (!is_array($paymentPlans)) $paymentPlans = [];
        $keepPaymentIds = [];

        foreach ($paymentPlans as $row) {
            if (!is_array($row)) continue;
            $rid = isset($row['id']) ? (int)$row['id'] : 0;

            $data = [
                'title' => trim((string)($row['title'] ?? '')),
                'value' => trim((string)($row['value'] ?? '')),
                'sub_title' => $toStr($row['sub_title'] ?? null),
                'project_id' => (int)$id,
                'active' => 1,
                'updated_at' => $now,
            ];

            if ($rid > 0) {
                DB::table('paymentplan_projects')->where('id', $rid)->where('project_id', (int)$id)->update($data);
                $keepPaymentIds[] = $rid;
            } else {
                $data['created_at'] = $now;
                $newId = DB::table('paymentplan_projects')->insertGetId($data);
                $keepPaymentIds[] = (int)$newId;
            }
        }

        DB::table('paymentplan_projects')
            ->where('project_id', (int)$id)
            ->when(count($keepPaymentIds) > 0, fn($q) => $q->whereNotIn('id', $keepPaymentIds))
            ->delete();

        // ---------- Locations (✅ project_locations) ----------
        $locations = $pget('locations', []);
        if (!is_array($locations)) $locations = [];
        $keepLocationIds = [];

        foreach ($locations as $row) {
            if (!is_array($row)) continue;
            $rid = isset($row['id']) ? (int)$row['id'] : 0;

            $data = [
                'title' => trim((string)($row['title'] ?? '')),
                'description' => trim((string)($row['description'] ?? '')),
                'map_link' => $toStr($row['map_link'] ?? null),
                // main_image exists in DB but you said removed in UI; keep as-is if sent, otherwise ignore
                'updated_at' => $now,
            ];

            // Optional: if you ever send main_image in payload
            if (array_key_exists('main_image', $row)) {
                $data['main_image'] = $toStr($row['main_image'] ?? null);
            }

            if ($rid > 0) {
                DB::table('project_locations')->where('id', $rid)->where('project_id', (int)$id)->update($data);
                $keepLocationIds[] = $rid;
            } else {
                $data['project_id'] = (int)$id;
                $data['created_at'] = $now;
                $newId = DB::table('project_locations')->insertGetId($data);
                $keepLocationIds[] = (int)$newId;
            }
        }

        DB::table('project_locations')
            ->where('project_id', (int)$id)
            ->when(count($keepLocationIds) > 0, fn($q) => $q->whereNotIn('id', $keepLocationIds))
            ->delete();

       // ---------- USP (project_unique_selling_points) ----------
$uspArr = $pget('unique_selling_points', []);
if (!is_array($uspArr)) $uspArr = [];

if (count($uspArr) === 0) {
    DB::table('project_unique_selling_points')->where('project_id', (int)$id)->delete();
} else {
    $row = $uspArr[0];
    if (!is_array($row)) $row = [];

    $rid = isset($row['id']) ? (int)$row['id'] : 0;

    // find existing USP row for this project (latest)
    $existingUsp = DB::table('project_unique_selling_points')
        ->where('project_id', (int)$id)
        ->orderByDesc('id')
        ->first();

    // keep old image unless replaced
    $uspImageFilename = $existingUsp->main_image ?? null;

    // if payload sends a filename, use it (only if not empty)
    if (!empty($row['main_image'])) {
        $uspImageFilename = (string)$row['main_image'];
    }

    // if new file uploaded, override everything
    if (!empty($uspUploadedFilename)) {
        $uspImageFilename = $uspUploadedFilename;
    }

    $data = [
        'title'       => trim((string)($row['title'] ?? '')),
        'description' => (string)($row['description'] ?? ''),
        'main_image'  => $uspImageFilename,
        'project_id'  => (int)$id,
        'updated_at'  => $now,
    ];

    if ($rid > 0) {
        // update by id (safe)
        DB::table('project_unique_selling_points')
            ->where('id', $rid)
            ->where('project_id', (int)$id)
            ->update($data);
    } else {
        // if no id provided, update existing by project_id, else insert
        if ($existingUsp) {
            DB::table('project_unique_selling_points')
                ->where('id', (int)$existingUsp->id)
                ->update($data);
        } else {
            $data['created_at'] = $now;
            DB::table('project_unique_selling_points')->insert($data);
        }
    }
}

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully.',
            'data' => ['project_id' => (int)$id],
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
