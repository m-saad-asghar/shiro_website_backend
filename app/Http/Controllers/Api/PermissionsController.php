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
use Illuminate\Http\JsonResponse;


class PermissionsController extends Controller
{

public function updateRolePermissions(Request $request)
{
    $validated = $request->validate([
        'role_id' => ['required', 'integer', 'exists:roles,id'],
        'permission_ids' => ['required', 'array', 'min:1'],
        'permission_ids.*' => ['integer', 'distinct', 'exists:permissions,id'],
    ]);

    $roleId = (int) $validated['role_id'];

    // Normalize incoming IDs
    $newPermissionIds = array_values(array_unique(array_map('intval', $validated['permission_ids'])));
    sort($newPermissionIds);

    // Current IDs from DB
    $currentPermissionIds = DB::table('role_has_permissions')
        ->where('role_id', $roleId)
        ->pluck('permission_id')
        ->map(fn ($v) => (int) $v)
        ->values()
        ->all();

    sort($currentPermissionIds);

    // ✅ If nothing changed, no DB write (still return Saved)
    if ($currentPermissionIds === $newPermissionIds) {
        return response()->json([
            'message' => 'Saved',
            'data' => [
                'role_id' => $roleId,
                'permission_ids' => $newPermissionIds,
            ],
        ], 200);
    }

    DB::beginTransaction();

    try {
        // Replace ONLY for this role_id
        DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->delete();

        $insertData = [];
        foreach ($newPermissionIds as $permissionId) {
            $insertData[] = [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ];
        }

        DB::table('role_has_permissions')->insert($insertData);

        DB::commit();

        return response()->json([
            'message' => 'Saved',
            'data' => [
                'role_id' => $roleId,
                'permission_ids' => $newPermissionIds,
            ],
        ], 200);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Failed to update',
            'error' => $e->getMessage(),
        ], 500);
    }
}
 public function storeRolePermissions(Request $request)
{
    $validated = $request->validate([
        'role_id' => ['required', 'integer', 'exists:roles,id'],
        'permission_ids' => ['required', 'array', 'min:1'],
        'permission_ids.*' => ['integer', 'distinct', 'exists:permissions,id'],
    ]);

    $roleId = (int) $validated['role_id'];
    $permissionIds = array_unique($validated['permission_ids']);

    // 🔥 CHECK IF ROLE ALREADY HAS PERMISSIONS
    $alreadyExists = DB::table('role_has_permissions')
        ->where('role_id', $roleId)
        ->exists();

    if ($alreadyExists) {
        return response()->json([
            'message' => 'This role already has permissions. Cannot override.'
        ], 409); // 409 = Conflict
    }

    DB::beginTransaction();

    try {

        $insertData = [];

        foreach ($permissionIds as $permissionId) {
            $insertData[] = [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ];
        }

        DB::table('role_has_permissions')->insert($insertData);

        DB::commit();

        return response()->json([
            'message' => 'Saved',
            'data' => [
                'role_id' => $roleId,
                'permission_ids' => array_values($permissionIds),
            ]
        ], 200);

    } catch (\Throwable $e) {

        DB::rollBack();

        return response()->json([
            'message' => 'Failed to save',
            'error' => $e->getMessage()
        ], 500);
    }
}

   public function all_permissions(Request $request)
    {
        $permissions = DB::table('permissions')
            ->select('id', 'title', 'name')
            ->orderBy('title')
            ->get();

        return response()->json([
            'message' => 'All permissions',
            'data' => $permissions,
        ]);
    }

public function get_permissions(): JsonResponse
{
    try {
        // 1) Fetch roles + their permissions (join)
        $rows = DB::table('roles as r')
            ->leftJoin('role_has_permissions as rhp', 'rhp.role_id', '=', 'r.id')
            ->leftJoin('permissions as p', 'p.id', '=', 'rhp.permission_id')
            ->select(
                'r.id as role_id',
                'r.title as role_title',
                'r.name as role_name',
                'r.guard_name as role_guard_name',
                'p.id as permission_id',
                'p.title as permission_title',
                'p.name as permission_name',
                'p.guard_name as permission_guard_name'
            )
            ->orderBy('r.id', 'desc')
            ->orderBy('p.id', 'desc')
            ->get();

        // 2) Group into: role => [permissions...]
        $grouped = [];
        foreach ($rows as $row) {
            $rid = (int) $row->role_id;

            if (!isset($grouped[$rid])) {
                $grouped[$rid] = [
                    'id' => $rid,
                    'title' => $row->role_title,
                    'name' => $row->role_name,
                    'guard_name' => $row->role_guard_name,
                    'permissions' => [],
                ];
            }

            // leftJoin means permission can be null (role has no permissions)
            if (!is_null($row->permission_id)) {
                $grouped[$rid]['permissions'][] = [
                    'id' => (int) $row->permission_id,
                    'title' => $row->permission_title,
                    'name' => $row->permission_name,
                    'guard_name' => $row->permission_guard_name,
                ];
            }
        }

        // reindex as array (frontend friendly)
        $data = array_values($grouped);

        return response()->json([
            'message' => 'Roles and permissions fetched successfully.',
            'data' => $data,
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Something went wrong.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

}