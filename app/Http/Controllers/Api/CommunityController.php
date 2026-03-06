<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

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

    public function fetchCommuntiesForAdminPanel(Request $request)
    {
        // per_page from frontend, default 10, hard-limit to avoid abuse
        $perPage = (int) $request->query('per_page', 10);
        if ($perPage <= 0) $perPage = 10;
        if ($perPage > 100) $perPage = 100;

        // page from frontend (Laravel paginator reads ?page=)
        $query = DB::table('communities')
            ->select(['id', 'name', 'active', 'created_at', 'updated_at'])
            ->orderByDesc('id');

        // Optional: filter active (active=1/0)
        if ($request->has('active')) {
            $active = (int) $request->query('active');
            if ($active === 0 || $active === 1) {
                $query->where('active', $active);
            }
        }

        // Optional: search by name (?search=pool)
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $paginator = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Communities fetched successfully.',
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'last_page'    => $paginator->lastPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
        ], 200);
    }

      public function add_community(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string'],
    ]);
    $name = trim((string) $validated['name']);
    $exists = DB::table('communities')
        ->where(function ($q) use ($name) {
            $q->where('name', $name);
        })
        ->exists();

    if ($exists) {
        return response()->json(['message' => 'community already exists.'], 409);
    }

    $now = now();

    try {
        $communityId = DB::table('communities')->insertGetId([
            'name'       => $name,
            'slug' => Str::slug($name),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    } catch (\Throwable $e) {
        \Log::error('add_community failed', [
            'msg' => $e->getMessage(),
        ]);

        // Return real reason instead of silent 500
        return response()->json([
            'message' => 'Failed to create community.',
            'error'   => $e->getMessage(),
        ], 500);
    }

    $community = DB::table('communities')->where('id', $communityId)->first();

    return response()->json([
        'message' => 'Community created successfully.',
        'data'    => $community,
    ], 201);
}

public function update_community(Request $request, $id)
{
    $id = (int) $id;

    $validated = $request->validate([
        'name' => ['required', 'string'],
    ]);

    $name = trim((string) $validated['name']);

    // 1) check exists
    $community = DB::table('communities')->where('id', $id)->first();
    if (!$community) {
        return response()->json(['message' => 'Community not found.'], 404);
    }

    // 2) check duplicate name (excluding current id)
    $exists = DB::table('communities')
        ->where('id', '!=', $id)
        ->where('name', $name)
        ->exists();

    if ($exists) {
        return response()->json(['message' => 'Community already exists.'], 409);
    }

    try {
        DB::table('communities')->where('id', $id)->update([
            'name'       => $name,
            'updated_at' => now(),
        ]);
    } catch (\Throwable $e) {
        \Log::error('update_community failed', [
            'id'  => $id,
            'msg' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => 'Failed to update community.',
            'error'   => $e->getMessage(),
        ], 500);
    }

    $updated = DB::table('communities')->where('id', $id)->first();

    return response()->json([
        'message' => 'Community updated successfully.',
        'data'    => $updated,
    ], 200);
}

public function delete_community($id)
{
    $id = (int) $id;

    $exists = DB::table('communities')->where('id', $id)->exists();
    if (!$exists) {
        return response()->json(['message' => 'Community not found.'], 404);
    }

    try {
        DB::table('communities')->where('id', $id)->delete();
    } catch (\Throwable $e) {
        \Log::error('delete_community failed', [
            'id'  => $id,
            'msg' => $e->getMessage(),
        ]);

        return response()->json([
            'message' => 'Failed to delete community.',
            'error'   => $e->getMessage(),
        ], 500);
    }

    return response()->json([
        'message' => 'Community deleted successfully.',
    ], 200);
}

 public function changeStatusCommunity(Request $request)
{
    $validator = Validator::make($request->all(), [
        'community_id' => 'required|integer|exists:communities,id',
        'active'  => 'required|in:0,1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    DB::table('communities')
        ->where('id', $request->community_id)
        ->update([
            'active' => $request->active,
            'updated_at' => now(),
        ]);

    return response()->json([
        'message' => 'Community status updated successfully',
    ]);
}

}
