<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
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


}
