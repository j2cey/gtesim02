<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminrole = Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Simple UserResource']);

        $permissions = Permission::pluck('id','id')->all();

        $adminrole->syncPermissions($permissions);
        $adminrole->revokePermissionTo('permission-create');
        $adminrole->revokePermissionTo('permission-update');
        $adminrole->revokePermissionTo('permission-delete');
    }
}
