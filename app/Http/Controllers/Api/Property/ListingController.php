<?php

namespace App\Http\Controllers\Api\Property;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListingController extends Controller
{
 public function showListingsOptions(Request $request)
{
    $searchText = trim((string) $request->input('search_text', ''));

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
        $slugs = $request->input('search', []);

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

}
