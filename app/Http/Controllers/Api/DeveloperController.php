<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeveloperController extends Controller
{
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
            'projects.main_image as project_main_image'
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
}
