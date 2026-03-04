<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class AdminAuthController extends Controller
{
       public function update_role(Request $request)
{
    $validated = $request->validate([
            'role_id' => ['required', 'integer'],
            'title'   => ['required', 'string', 'max:100'],
        ]);

        $roleId = (int) $validated['role_id'];
        $title  = trim($validated['title']);

        // 2) Find role
        $role = Role::query()->find($roleId);
        if (!$role) {
            return response()->json([
                'message' => 'Role not found.',
            ], 404);
        }

        // 3) Generate name from title
        $newName = Str::snake($title);

        // 4) Guard name (keep same as existing OR force api)
        // Best: keep existing guard_name
        $guard = $role->guard_name ?? 'api';

        // 5) Check duplicates excluding this role id
        $exists = Role::query()
            ->where('guard_name', $guard)
            ->where('id', '!=', $roleId)
            ->where(function ($q) use ($newName, $title) {
                $q->where('name', $newName)
                  ->orWhere('title', $title);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Role already exists.',
            ], 409);
        }

        // 6) Update only title + name
        $role->title = $title;
        $role->name  = $newName;
        $role->save();

        // 7) Return success
        return response()->json([
            'message' => 'Role updated successfully.',
            'data' => [
                'id' => $role->id,
                'title' => $role->title,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'updated_at' => $role->updated_at,
            ],
        ], 200);
}
     public function add_role(Request $request)
{
     $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
        ]);

        $title = trim($validated['title']);

        // 2) Generate name from title (snake_case)
        $name = Str::snake($title);

        // 3) Decide guard_name (based on your DB screenshot)
        $guard = 'api';

        // 4) Check duplicate (by name OR title) within same guard
        $exists = Role::query()
            ->where('guard_name', $guard)
            ->where(function ($q) use ($name, $title) {
                $q->where('name', $name)
                  ->orWhere('title', $title);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Role already exists.',
            ], 409);
        }

        // 5) Create
        $role = Role::create([
            'title' => $title,
            'name' => $name,
            'guard_name' => $guard,
        ]);

        // 6) Return success
        return response()->json([
            'message' => 'Role created successfully.',
            'data' => [
                'id' => $role->id,
                'title' => $role->title,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ],
        ], 201);
}
    public function delete_role(Request $request)
{
    $validated = $request->validate([
        'role_id' => ['required', 'integer', 'exists:roles,id'],
    ]);

    $roleId = $validated['role_id'];

    // ❌ Prevent deleting Super Admin (optional safety rule)
    $role = DB::table('roles')->where('id', $roleId)->first();

    if (!$role) {
        return response()->json([
            'message' => 'Role not found.'
        ], 404);
    }

    if ($role->name === 'super_admin') {
        return response()->json([
            'message' => 'Super Admin role cannot be deleted.'
        ], 403);
    }

    // ✅ Remove role-user relationships first (Spatie)
    DB::table('model_has_roles')->where('role_id', $roleId)->delete();

    // ✅ Delete role
    DB::table('roles')->where('id', $roleId)->delete();

    return response()->json([
        'message' => 'Role deleted successfully.'
    ], 200);
}
    public function get_roles(Request $request)
    {
        $roles = DB::table('roles')
        ->select([
            'id',
            'title',
            'name',
            'guard_name',
            'created_at',
            'updated_at',
        ])
        ->orderBy('id', 'desc')
        ->get();

    return response()->json([
        'message' => 'Roles fetched successfully',
        'data' => $roles
    ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

            if ($user->active != 1) {
            return response()->json([
            'status' => false,
            'message' => 'User is deactivated.',
            ], 403);
        }

        $tokenResult = $user->createToken('admin-panel');
        $accessToken = $tokenResult->accessToken;
        $expiresAt   = $tokenResult->token->expires_at;

        return response()->json([
            'token_type'   => 'Bearer',
            'access_token' => '05ff4aa2ba011dc914cd463e62b0811341eeb70d1705e315f38fd029f18cf611f257b3c48f9ca562',
            // 'access_token' => $accessToken,
            // 'expires_at'   => $expiresAt,
            'expires_at'   => '',
            'user' => [
                'id'          => $user->id,
                'first_name'  => $user->first_name,
                'last_name'   => $user->last_name,
                'email'       => $user->email,
                'profile_image' => $user->profile_image,
                'roles'       => $user->getRoleNames(), // collection of role names
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id'          => $user->id,
            'first_name'  => $user->first_name,
            'last_name'   => $user->last_name,
            'email'       => $user->email,
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke current access token
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Logged out']);
    }

    public function store(Request $request)
    {
        // If you used middleware('role:super_admin') already, no need extra checks here.
        // If you didn’t, then enforce it here as backup:
        // if (!$request->user()->hasRole('super_admin')) abort(403, 'Not allowed.');
        $roleNames = Role::pluck('name')->toArray();

        $validated = $request->validate([
            // REQUIRED
            'first_name' => ['required', 'string', 'max:256'],
            'last_name'  => ['required', 'string', 'max:256'],
            'email'      => ['required', 'email', 'max:256', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8', 'max:72'],

            // OPTIONAL
            'phone_number' => ['nullable', 'string', 'max:50'],

            // If profile_image is a FILE upload (recommended)
            'profile_image' => ['nullable', 'image', 'max:2048'], // 2MB
            // If you are NOT uploading file and just storing a URL/path string, replace above with:
            // 'profile_image' => ['nullable', 'string', 'max:2048'],

            // OPTIONAL role assignment
           'role' => ['nullable', 'string', 'in:' . implode(',', $roleNames)],
        ]);

        $profileImagePath = null;

        // Handle file upload if sent as multipart/form-data
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profiles', 'public');
        } elseif (isset($validated['profile_image']) && is_string($validated['profile_image'])) {
            // If you're storing string URL/path instead of a file upload
            $profileImagePath = $validated['profile_image'];
        }

        $user = User::create([
            'first_name'    => $validated['first_name'],
            'last_name'     => $validated['last_name'],
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
            'phone_number'  => $validated['phone_number'] ?? null,
            'profile_image' => $profileImagePath,
        ]);

        // Assign role if provided (Spatie)
        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        } else {
            // Optional default role if you want:
            // $user->assignRole('writer');
        }

        return response()->json([
            'status' => true,
            'message' => 'User created successfully.',
            'data' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'profile_image' => $user->profile_image,
                'roles' => $user->getRoleNames(),
            ],
        ], 201);
    }
}