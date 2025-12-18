<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    public function show(string $slug)
    {
        $rows = DB::table('communities')
            ->leftJoin('projects', 'projects.community_id', '=', 'communities.id')
            ->where('communities.slug', $slug)
            ->select(
                'communities.id as community_id',
                'communities.name as community_name',
                'communities.description as community_description',
                'communities.slug as community_slug',
                'communities.main_image as community_main_image',
                'projects.id as project_id',
                'projects.name as project_name',
                'projects.slug as project_slug',
                'projects.description as project_description',
                'projects.main_image as project_main_image',
            )
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'message' => 'Project not found.',
            ], 404);
        }

        // First row = community info
        $communityRow = $rows->first();

        $projects = $rows
            ->filter(fn ($row) => !is_null($row->project_id))
            ->map(fn ($row) => [
                'id'          => $row->project_id,
                'name'        => $row->project_name,
                'slug'        => $row->project_slug,
                'description' => $row->project_description,
                'project_main_image' => $row->project_main_image,
            ])
            ->values()
            ->all();

        return response()->json([
            'community' => [
                'id'          => $communityRow->community_id,
                'name'        => $communityRow->community_name,
                'slug'        => $communityRow->community_slug,
                'description' => $communityRow->community_description,
                'community_main_image' => $communityRow->community_main_image,
            ],
            'projects' => $projects,
        ]);
    }
}
