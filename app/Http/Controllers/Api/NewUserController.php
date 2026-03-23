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
        'user_id'               => 'required|integer|exists:users,id',
        'first_name'            => 'required|string|max:100',
        'last_name'             => 'required|string|max:100',
        'email'                 => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($request->user_id),
        ],
        'phone_number'          => 'nullable|string|max:20',
        'password'              => 'nullable|string|min:6',
        'password_confirmation' => 'required_with:password|same:password',
        'profile_image'         => 'nullable|mimes:jpg,jpeg,png,webp,avif|max:5120',
        'role_id'               => 'required|integer|exists:roles,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    DB::beginTransaction();

    try {
        // Keep old image by default
        $imageName = $existingUser->profile_image;

        // Handle image upload (optional)
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            // Generate unique filename
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Save inside: storage/app/public/
            $file->storeAs('', $imageName, 'public');
        }

        // Prepare update data
        $updateData = [
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone_number'  => $request->phone_number,
            'profile_image' => $imageName,
            'updated_at'    => now(),
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Update user
        DB::table('users')
            ->where('id', $request->user_id)
            ->update($updateData);

        /*
        |--------------------------------------------------------------------------
        | Update role in model_has_roles
        |--------------------------------------------------------------------------
        | We keep existing model_type if already present.
        | If no role row exists yet, fallback to App\Models\User
        |--------------------------------------------------------------------------
        */
        $existingRoleRow = DB::table('model_has_roles')
            ->where('model_id', $request->user_id)
            ->first();

        $modelType = $existingRoleRow && !empty($existingRoleRow->model_type)
            ? $existingRoleRow->model_type
            : 'App\\Models\\User';

        // Remove old roles for this user
        DB::table('model_has_roles')
            ->where('model_id', $request->user_id)
            ->delete();

        // Insert new role
        DB::table('model_has_roles')->insert([
            'role_id'    => $request->role_id,
            'model_type' => $modelType,
            'model_id'   => $request->user_id,
        ]);

        // Fetch updated user with role
        $user = DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('users.id', $request->user_id)
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone_number',
                'users.profile_image',
                'users.active',
                'users.created_at',
                'users.updated_at',
                DB::raw('MAX(roles.title) as role'),
                DB::raw('MAX(roles.name) as role_name')
            )
            ->groupBy(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone_number',
                'users.profile_image',
                'users.active',
                'users.created_at',
                'users.updated_at'
            )
            ->first();

        DB::commit();

        return response()->json([
            'message' => 'User updated successfully',
            'data'    => $user,
        ], 200);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Something went wrong while updating user',
            'error'   => $e->getMessage(),
        ], 500);
    }
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
        'first_name'            => 'required|string|max:100',
        'last_name'             => 'required|string|max:100',
        'email'                 => 'required|email|unique:users,email',
        'phone_number'          => 'nullable|string|max:20',
        'password'              => 'required|string|min:6',
        'password_confirmation' => 'required|same:password',
        'profile_image'         => 'nullable|mimes:jpg,jpeg,png,webp,avif|max:5120',
        'role_id'               => 'required|integer|exists:roles,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    DB::beginTransaction();

    try {
        // Handle image upload (optional)
        $imageName = null;

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            // Generate unique filename
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Save inside: storage/app/public/
            $file->storeAs('', $imageName, 'public');
        }

        // Insert user
        $userId = DB::table('users')->insertGetId([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'phone_number'  => $request->phone_number,
            'password'      => Hash::make($request->password),
            'profile_image' => $imageName,
            'active'        => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Save role in model_has_roles
        |--------------------------------------------------------------------------
        | Fallback model_type used below.
        | If your project uses a different model_type string, change it.
        |--------------------------------------------------------------------------
        */
        $modelType = 'App\\Models\\User';

        DB::table('model_has_roles')->insert([
            'role_id'    => $request->role_id,
            'model_type' => $modelType,
            'model_id'   => $userId,
        ]);

        // Fetch created user with role
        $user = DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('users.id', $userId)
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone_number',
                'users.profile_image',
                'users.active',
                'users.created_at',
                'users.updated_at',
                DB::raw('MAX(roles.title) as role'),
                DB::raw('MAX(roles.name) as role_name')
            )
            ->groupBy(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone_number',
                'users.profile_image',
                'users.active',
                'users.created_at',
                'users.updated_at'
            )
            ->first();

        DB::commit();

        return response()->json([
            'message' => 'User created successfully',
            'data'    => $user,
        ], 201);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Something went wrong while creating user',
            'error'   => $e->getMessage(),
        ], 500);
    }
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
        ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.phone_number',
            'users.profile_image',
            'users.active',
            'users.created_at',
            'users.updated_at',
            DB::raw('MAX(roles.title) as role'),
            DB::raw('MAX(roles.name) as role_name')
        );

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('users.first_name', 'LIKE', "%{$search}%")
              ->orWhere('users.last_name', 'LIKE', "%{$search}%")
              ->orWhere('users.email', 'LIKE', "%{$search}%")
              ->orWhere('users.phone_number', 'LIKE', "%{$search}%");
        });
    }

    $users = $query
        ->groupBy(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.phone_number',
            'users.profile_image',
            'users.active',
            'users.created_at',
            'users.updated_at'
        )
        ->orderByDesc('users.id')
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
