<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function fetchAboutUsContent()
    {
        $data = DB::table('about_us_content')
            ->select('heading', 'title', 'description')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
    public function messageFromFounder(Request $request): JsonResponse
{
    $name = $request->input('name');

    $data = DB::table('about_us_page')
        ->when($name, function ($query) use ($name) {
            $query->where('name', $name);
        })
        ->orderBy('id', 'asc')
        ->first(); // ✅ only first matched record

    if (!$data) {
        return response()->json([
            'status' => false,
            'message' => 'No record found'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $data
    ], 200);
}
    public function show(): JsonResponse
    {
        $employees = DB::table('employees')
            ->select('id', 'name', 'position', 'slug', 'profile_picture', 'description')
            ->where('active', 1)
            ->where('in_contact_page', 1)
            ->where('is_agent', 0)
            ->orderByRaw('sorting IS NULL, sorting ASC')
            ->orderBy('id', 'asc')
            ->get();
        
        $agents = DB::table('employees')
            ->select('id', 'name', 'position', 'slug', 'profile_picture', 'phone', 'whatsapp', 'email', 'description')
            ->where('active', 1)
            ->where('in_contact_page', 1)
            ->where('is_agent', 1)
            ->orderByRaw('sorting IS NULL, sorting ASC')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $employees,
            'agents' => $agents,
        ]);
    }

  public function showDetails(string $slug): JsonResponse
{
    $employee = DB::table('employees')
        ->where('slug', $slug)
        ->where('active', 1)
        ->first();

    if (!$employee) {
        return response()->json([
            'status' => 0,
            'message' => 'Employee not found',
        ], 404);
    }

    // ✅ ADD: listings (match by employee.crm_name = listings.agent)
    $crmName = trim((string)($employee->crm_name ?? ''));
    if ($crmName === '') {
        $crmName = trim((string)($employee->name ?? ''));
    }

    $listings = [];
    if ($crmName !== '') {
        $listings = DB::table('listings')
            ->select(
                'id',
                'reference',
                'title',
                'price',
                'bedrooms',
                'bathrooms',
                'area',
                'community',
                'sub_community',
                'property',
                'active',
                'is_featured',
                'property_type',
                'property_category',
                'project_status'
            )
            ->where('agent', $crmName)
            ->where('active', 1)
            ->orderByDesc('id')
            ->get()
            ->map(function ($listing) {
                // ✅ attach images exactly like your screenshot style (array of urls)
                $listing->images = DB::table('listing_images')
                    ->where('listing_id', $listing->id)
                    ->orderByRaw('COALESCE(sorting, 999999) ASC')
                    ->pluck('image')
                    ->filter()
                    ->values();

                return $listing;
            })
            ->values();
    }

    $listings = $listings->map(function ($listing) {

    // ✅ attach images (same as your screenshot structure)
    $images = DB::table('listing_images')
        ->where('listing_id', $listing->id)
        ->orderByRaw('COALESCE(sorting, 999999) ASC')
        ->pluck('image')
        ->filter()
        ->values();

    return [
        'reference'         => $listing->reference,
        'id'                => $listing->id,
        'bedrooms'          => $listing->bedrooms,
        'bathrooms'         => $listing->bathrooms,
        'price'             => $listing->price,
        'area'              => $listing->area,
        'title'             => $listing->title,
        'community'         => $listing->community,
        'sub_community'     => $listing->sub_community,
        'property'          => $listing->property,
        'active'            => $listing->active,
        'is_featured'       => $listing->is_featured,
        'property_type'     => $listing->property_type,
        'property_category' => $listing->property_category,
        'project_status'    => $listing->project_status,
        'images'            => $images,

        // ✅ ADD COMPANY CONTACT HERE
        'company_contact' => [
            'phone'    => env('COMPANY_PHONE'),
            'whatsapp' => env('COMPANY_WHATSAPP'),
            'email'    => env('COMPANY_EMAIL'),
        ],
    ];
})->values();


    return response()->json([
        'status' => 1,
        'data' => $employee,
        'listings' => $listings, // ✅ ADD THIS (frontend will show 3)
    ]);
}
}