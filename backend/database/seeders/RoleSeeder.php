<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::create(['name' => 'admin']);
        $role_editor = Role::create(['name' => 'editor']);
        $role_user = Role::create(['name' => 'user']);

        $permission_post_read = Permission::create(['name' => 'read posts']);
        $permission_post_edit = Permission::create(['name' => 'edit posts']);
        $permission_post_write = Permission::create(['name' => 'write posts']);
        $permission_post_delete = Permission::create(['name' => 'delete posts']);

        $permission_create_user = Permission::create(['name' => 'create user']);
        $permission_edit_user = Permission::create(['name' => 'edit user']);
        $permission_delete_user = Permission::create(['name' => 'delete user']);

        $permissions_admin = [$permission_delete_user, $permission_create_user, $permission_edit_user];
        $permissions_editor = [$permission_post_read, $permission_post_edit, $permission_post_write, $permission_post_delete];

        $role_admin->syncPermissions($permissions_admin, $permissions_editor);
        $role_editor->syncPermissions($permissions_editor);
        $role_user->givePermissionTo($permission_post_read);
    }
}
