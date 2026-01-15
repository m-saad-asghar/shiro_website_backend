<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function allAreas(Request $request)
    {
        try {
            $perPage = (int) $request->get('per_page', 12);
            $perPage = $perPage > 0 ? $perPage : 12;

            $areas = DB::table('communities')
                ->select([
                    'id',
                    'name',
                    'slug',
                    'description',
                    'main_image',
                ])
                ->where('active', 1)
                ->where('is_area', 1)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . trim($request->search) . '%');
                })
                ->orderBy('name', 'asc')
                ->paginate($perPage);

            return response()->json([
                'data' => [
                    'areas' => $areas->items(),
                    'pagination' => [
                        'current_page' => $areas->currentPage(),
                        'last_page'    => $areas->lastPage(),
                        'per_page'     => $areas->perPage(),
                        'total'        => $areas->total(),
                    ],
                ],
                'status' => true,
                'error' => null,
                'statusCode' => 200,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'data' => null,
                'status' => false,
                'error' => $e->getMessage(),
                'statusCode' => 500,
            ], 500);
        }
    }

   public function fetchAreaDetails(string $slug)
{
    $community = DB::table('communities')
        ->select([
            'id',
            'name',
            'slug',
            'main_image',
            'description',
            'selling_point',
            'about'
        ])
        ->where('slug', $slug)
        ->first();

    if (!$community) {
        return response()->json([
            'success' => false,
            'message' => 'Community not found.',
            'data' => null,
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $community->id,
            'name' => $community->name,
            'slug' => $community->slug,
            'main_image' => $community->main_image,
            'description' => $community->description,
            'selling_point' => $community->selling_point,
            'about' => $community->about,
        ],
    ], 200);
}
}