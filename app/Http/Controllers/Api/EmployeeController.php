<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
    $name = $request->query('name');

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

 public function fetchEmployees(Request $request)
{
    $perPage = (int) $request->query('per_page', 10);
    $perPage = max(1, min($perPage, 100));

    $page = (int) $request->query('page', 1);
    $search = trim($request->query('search', ''));

    $query = DB::table('employees')
        ->select([
            'id',
            'name',
            'position',
            'profile_picture',
            'email',
            'phone',
            'whatsapp',
            'department',
            'active',
            'created_at',
        ]);

    // 🔍 Search Support (first_name + last_name)
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%");
        });
    }

    $users = $query
        ->orderByDesc('id')
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json([
        'success' => true,
        'data' => $users->items(),
        'pagination' => [
            'current_page' => $users->currentPage(),
            'last_page'    => $users->lastPage(),
            'per_page'     => $users->perPage(),
            'total'        => $users->total(),
        ]
    ]);
}

 public function changeStatusEmployee(Request $request)
{
      $validator = Validator::make($request->all(), [
        'employee_id' => 'required|integer|exists:employees,id',
        'active'  => 'required|in:0,1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    DB::table('employees')
        ->where('id', $request->employee_id)
        ->update([
            'active' => $request->active,
        ]);

    return response()->json([
        'message' => 'Employee status updated successfully',
    ]);
}

  public function fetchPosition()
    {
        $rows = DB::table('positions')
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'positions' => $rows,
            ],
        ]);
    }

     public function fetchDepartments()
    {
        $rows = DB::table('departments')
            ->select('id', 'title')
            ->orderBy('title')
            ->where('active', 1)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'departments' => $rows,
            ],
        ]);
    }

     public function fetchCrmAgents()
    {
        $rows = DB::table('agents')
            ->select('id', 'name')
            ->orderBy('name')
            ->where('active', 1)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'agents' => $rows,
            ],
        ]);
    }

public function createEmployee(Request $request)
{
    try {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:256',
            'slug' => 'required|string|max:256|unique:employees,slug',
            'position' => 'required|string|max:256',
            'position_id' => 'required|integer',
            'department' => 'required|string|max:256',
            'department_id' => 'required|integer',

            'crm_name' => 'nullable|string|max:256',

            'email' => 'required|email',
            'phone' => 'required|string|max:50',

            'whatsapp' => 'nullable|string|max:50',
            'linkedin' => 'nullable|string',
            'orn' => 'nullable|integer',
            'sorting' => 'nullable|integer',
            'brn' => 'nullable|integer',
            'experience_since' => 'nullable|integer',
            'description' => 'nullable|string',

            'is_director' => 'nullable|in:0,1',
            'in_contact_page' => 'nullable|in:0,1',
            'is_agent' => 'nullable|in:0,1',
            'active' => 'nullable|in:0,1',

            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $fileName = null;

        if ($request->hasFile('profile_picture')) {

            $file = $request->file('profile_picture');

            $fileName = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

            $file->move(storage_path('app/public/emp'), $fileName);
        }

        DB::table('employees')->insert([
            'name' => $request->name,
            'slug' => $request->slug,

            'position' => $request->position,
            'position_id' => $request->position_id,

            'department' => $request->department,
            'department_id' => $request->department_id,

            'crm_name' => $request->crm_name,

            'email' => $request->email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,

            'linkedin' => $request->linkedin,
            'orn' => $request->orn,
            'sorting' => $request->sorting,
            'brn' => $request->brn,

            'experience_since' => $request->experience_since,
            'description' => $request->description,

            'is_director' => $request->is_director ?? 0,
            'in_contact_page' => $request->in_contact_page ?? 0,
            'is_agent' => $request->is_agent ?? 0,
            'active' => $request->active ?? 1,

            'profile_picture' => $fileName,

            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}

public function getEmployee(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = DB::table('employees')
            ->where('id', $request->employee_id)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee fetched successfully',
            'data' => $employee
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function updateEmployee(Request $request)
{
    try {

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',

            'name' => 'required|string|max:256',
            'slug' => 'required|string|max:256|unique:employees,slug,' . $request->employee_id,

            'position' => 'required|string|max:256',
            'position_id' => 'required|integer',

            'department' => 'required|string|max:256',
            'department_id' => 'required|integer',

            'crm_name' => 'nullable|string|max:256',

            'email' => 'required|email',
            'phone' => 'required|string|max:50',

            'whatsapp' => 'nullable|string|max:50',
            'linkedin' => 'nullable|string',
            'orn' => 'nullable|integer',
            'sorting' => 'nullable|integer',
            'brn' => 'nullable|integer',
            'experience_since' => 'nullable|integer',
            'description' => 'nullable|string',

            'is_director' => 'nullable|in:0,1',
            'in_contact_page' => 'nullable|in:0,1',
            'is_agent' => 'nullable|in:0,1',
            'active' => 'nullable|in:0,1',

            'remove_profile_picture' => 'nullable|in:0,1',

            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = DB::table('employees')
            ->where('id', $request->employee_id)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $fileName = $employee->profile_picture;

        $folderPath = storage_path('app/public/emp');

        // remove old image if requested
        if ($request->remove_profile_picture == '1' && !empty($employee->profile_picture)) {
            $oldFilePath = $folderPath . '/' . $employee->profile_picture;

            if (file_exists($oldFilePath)) {
                @unlink($oldFilePath);
            }

            $fileName = null;
        }

        // upload new image and remove old one
        if ($request->hasFile('profile_picture')) {

            if (!empty($employee->profile_picture)) {
                $oldFilePath = $folderPath . '/' . $employee->profile_picture;

                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }

            $file = $request->file('profile_picture');
            $fileName = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($folderPath, $fileName);
        }

        DB::table('employees')
            ->where('id', $request->employee_id)
            ->update([
                'name' => $request->name,
                'slug' => $request->slug,

                'position' => $request->position,
                'position_id' => $request->position_id,

                'department' => $request->department,
                'department_id' => $request->department_id,

                'crm_name' => $request->crm_name,

                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp' => $request->whatsapp,

                'linkedin' => $request->linkedin,
                'orn' => $request->orn,
                'sorting' => $request->sorting,
                'brn' => $request->brn,

                'experience_since' => $request->experience_since,
                'description' => $request->description,

                'is_director' => $request->is_director ?? 0,
                'in_contact_page' => $request->in_contact_page ?? 0,
                'is_agent' => $request->is_agent ?? 0,
                'active' => $request->active ?? 1,

                'profile_picture' => $fileName,

                // 'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);

    }
}

public function getDepartments(Request $request)
{
    $page = (int) $request->input('page', 1);
    $perPage = (int) $request->input('per_page', 10);
    $search = trim((string) $request->input('search', ''));

    $query = DB::table('departments');

    if ($search !== '') {
        $query->where('title', 'LIKE', "%{$search}%");
    }

    $total = $query->count();

    $departments = $query
        ->orderByDesc('id')
        ->offset(($page - 1) * $perPage)
        ->limit($perPage)
        ->get();

    $data = $departments->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->title,
            'active' => $item->active,
            'created_at' => $item->created_at,
            'updated_at' => null,
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Departments fetched successfully',
        'data' => $data,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage),
            'total' => $total,
            'from' => ($page - 1) * $perPage + 1,
            'to' => ($page - 1) * $perPage + $data->count(),
        ],
    ]);
}

public function addDepartment(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:256|unique:departments,title'
    ]);

    $id = DB::table('departments')->insertGetId([
        'title' => $request->name,
        'active' => 1,
        'created_at' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Department created successfully',
        'data' => [
            'id' => $id,
            'name' => $request->name,
            'active' => 1
        ]
    ]);
}

public function updateDepartment(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:256|unique:departments,title,' . $id
    ]);

    DB::table('departments')
        ->where('id', $id)
        ->update([
            'title' => $request->name
        ]);

    return response()->json([
        'success' => true,
        'message' => 'Department updated successfully'
    ]);
}

public function deleteDepartment($id)
{
    DB::table('departments')
        ->where('id', $id)
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Department deleted successfully'
    ]);
}

public function changeStatusDepartment(Request $request)
{
    $request->validate([
        'department_id' => 'required|exists:departments,id',
        'active' => 'required|in:0,1'
    ]);

    DB::table('departments')
        ->where('id', $request->department_id)
        ->update([
            'active' => $request->active
        ]);

    return response()->json([
        'success' => true,
        'message' => 'Department status updated successfully'
    ]);
}

public function getPositions(Request $request)
{
    $page = (int) $request->input('page', 1);
    $perPage = (int) $request->input('per_page', 10);
    $search = trim((string) $request->input('search', ''));

    $query = DB::table('positions');

    if ($search !== '') {
        $query->where('title', 'LIKE', "%{$search}%");
    }

    $total = $query->count();

    $positions = $query
        ->orderByDesc('id')
        ->offset(($page - 1) * $perPage)
        ->limit($perPage)
        ->get();

    $data = $positions->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->title, // frontend expects name
            'created_at' => $item->created_at,
            'updated_at' => null,
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Positions fetched successfully',
        'data' => $data,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) ceil($total / $perPage),
            'total' => $total,
            'from' => $total > 0 ? (($page - 1) * $perPage) + 1 : null,
            'to' => $data->count() > 0 ? (($page - 1) * $perPage) + $data->count() : null,
        ],
    ]);
}

public function addPosition(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:256|unique:positions,title',
    ]);

    $id = DB::table('positions')->insertGetId([
        'title' => $request->name,
        'created_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Position created successfully',
        'data' => [
            'id' => $id,
            'name' => $request->name,
        ],
    ]);
}

public function updatePosition(Request $request, $id)
{
    $exists = DB::table('positions')->where('id', $id)->exists();

    if (!$exists) {
        return response()->json([
            'success' => false,
            'message' => 'Position not found',
        ], 404);
    }

    $request->validate([
        'name' => 'required|string|max:256|unique:positions,title,' . $id,
    ]);

    DB::table('positions')
        ->where('id', $id)
        ->update([
            'title' => $request->name,
        ]);

    return response()->json([
        'success' => true,
        'message' => 'Position updated successfully',
    ]);
}

public function deletePosition($id)
{
    $exists = DB::table('positions')->where('id', $id)->exists();

    if (!$exists) {
        return response()->json([
            'success' => false,
            'message' => 'Position not found',
        ], 404);
    }

    DB::table('positions')
        ->where('id', $id)
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Position deleted successfully',
    ]);
}

}