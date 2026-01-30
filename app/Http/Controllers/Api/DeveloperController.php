<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeveloperController extends Controller
{
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

}
