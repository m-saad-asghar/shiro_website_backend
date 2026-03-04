<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // user management
            'users.create', 'users.view', 'users.update', 'users.delete',

            // content / writer
            'posts.create', 'posts.view', 'posts.update', 'posts.delete',

            // example "components"
            'properties.view', 'properties.update',
            'settings.view', 'settings.update',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin      = Role::firstOrCreate(['name' => 'admin']);
        $writer     = Role::firstOrCreate(['name' => 'writer']);

        // super_admin: everything
        $superAdmin->syncPermissions(Permission::all());

        // admin: everything except creating users
        $admin->syncPermissions(array_values(array_diff($permissions, ['users.create'])));

        // writer: limited content perms
        $writer->syncPermissions(['posts.create', 'posts.view', 'posts.update']);
    }
}