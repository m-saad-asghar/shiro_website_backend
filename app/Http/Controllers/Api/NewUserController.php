<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class NewUserController extends Controller
{

public function update_user(Request $request)
{
    $existingUser = DB::table('users')->where('id', $request->user_id)->first();

    if (!$existingUser) {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'user_id'        => 'required|integer|exists:users,id',
        'first_name'     => 'required|string|max:100',
        'last_name'      => 'required|string|max:100',

        // ✅ IMPORTANT: unique email BUT ignore current user_id
        'email'          => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($request->user_id),
        ],

        'phone_number'   => 'nullable|string|max:20',

        // ✅ Password is OPTIONAL on update
        'password'       => 'nullable|string|min:6',

        // ✅ confirm_password only required if password is sent
        'password_confirmation' => 'required_with:password|same:password',

        'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }
    

    // ✅ Handle image upload (optional)
    $imageName = $existingUser->profile_image; // keep old by default

    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');

        // Generate unique filename
        $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Save inside: storage/app/public/admin_panel/users
        $file->storeAs('admin_panel/users', $imageName, 'public');
    }

    // ✅ Prepare update data
    $updateData = [
        'first_name'   => $request->first_name,
        'last_name'    => $request->last_name,
        'email'        => $request->email,
        'phone_number' => $request->phone_number,
        'profile_image'=> $imageName,
        'updated_at'   => now(),
    ];

    // ✅ Only update password if user provided it (do NOT override with null/empty)
    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    // ✅ Update user
    DB::table('users')->where('id', $request->user_id)->update($updateData);

    // ✅ Fetch updated user
    $user = DB::table('users')->where('id', $request->user_id)->first();

    return response()->json([
        'message' => 'User updated successfully',
        'data'    => $user,
    ], 200);
}

    public function delete_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $userId = (int) $request->user_id;

        // Fetch user (DB, not model)
        $user = DB::table('users')
            ->select('id', 'profile_image')
            ->where('id', $userId)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Delete image if exists
        if (!empty($user->profile_image)) {
            $filenameOrPath = ltrim((string) $user->profile_image, '/');

            // If you store ONLY filename, this is the main expected location:
            $expected = "admin_panel/users/{$userId}/{$filenameOrPath}";

            // Also try common variants (in case old records stored path)
            $candidates = array_values(array_unique([
                $expected,
                "admin_panel/users/{$filenameOrPath}",
                $filenameOrPath,
            ]));

            foreach ($candidates as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            // If your design is 1 folder per user, remove the folder too (optional)
            $userDir = "admin_panel/users/{$userId}";
            if (Storage::disk('public')->exists($userDir)) {
                Storage::disk('public')->deleteDirectory($userDir);
            }
        }

        // Delete DB row
        DB::table('users')->where('id', $userId)->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }
  public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'first_name'        => 'required|string|max:100',
        'last_name'         => 'required|string|max:100',
        'email'             => 'required|email|unique:users,email',
        'phone_number'      => 'nullable|string|max:20',
        'password'          => 'required|string|min:6',
        'confirm_password'  => 'required|same:password',
        'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,avif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    // ✅ Handle image upload (optional)
   $imageName = null;

if ($request->hasFile('profile_image')) {

    $file = $request->file('profile_image');

    // Generate unique filename
    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

    // Save inside: storage/app/public/admin_panel/users
    $file->storeAs('admin_panel/users', $imageName, 'public');
}

// ✅ Insert using DB
$userId = DB::table('users')->insertGetId([
    'first_name'   => $request->first_name,
    'last_name'    => $request->last_name,
    'email'        => $request->email,
    'phone_number' => $request->phone_number,
    'password'     => Hash::make($request->password),
    'profile_image'=> $imageName, // only filename
    'active'       => 1,
    'created_at'   => now(),
    'updated_at'   => now(),
]);

    // ✅ Fetch created user
    $user = DB::table('users')->where('id', $userId)->first();

    return response()->json([
        'message' => 'User created successfully',
        'data'    => $user,
    ], 201);
}



    public function changeStatusUser(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|integer|exists:users,id',
        'active'  => 'required|in:0,1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    DB::table('users')
        ->where('id', $request->user_id)
        ->update([
            'active' => $request->active,
            'updated_at' => now(),
        ]);

    return response()->json([
        'message' => 'User status updated successfully',
    ]);
}
     public function index(Request $request)
{
    $perPage = (int) $request->query('per_page', 10);
    $perPage = max(1, min($perPage, 100));

    $page = (int) $request->query('page', 1);
    $search = trim($request->query('search', ''));

    $query = DB::table('users')
        ->select([
            'id',
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'profile_image',
            'active',
            'created_at',
            'updated_at',
        ]);

    // 🔍 Search Support (first_name + last_name)
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%");
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

   
}
