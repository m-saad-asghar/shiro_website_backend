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
            ->select('title', 'description')
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
            ->select('id', 'name', 'position', 'slug', 'profile_picture')
            ->where('active', 1)
            ->where('in_contact_page', 1)
            ->orderByRaw('sorting IS NULL, sorting ASC')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $employees,
        ]);
    }
}