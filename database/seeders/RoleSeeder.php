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
        //$adminrole = Role::upsert(['name' => 'Admin'], uniqueBy: ['name' => 'Admin'], update: ['name' => 'Admin']);
        $adminrole = Role::firstOrCreate (['name' => 'Admin'], ['name' => 'Admin']);

        //$simpleuser_role = Role::upsert(['name' => 'Simple UserResource'], uniqueBy: ['name' => 'Simple UserResource'], update: ['name' => 'Simple UserResource']);
        $simpleuser_role = Role::firstOrCreate (['name' => 'Simple UserResource'], ['name' => 'Simple UserResource']);

        $permissions = Permission::pluck('id','id')->all();

        $adminrole->syncPermissions($permissions);
        //$adminrole->revokePermissionTo('permission-create');
        //$adminrole->revokePermissionTo('permission-update');
        //$adminrole->revokePermissionTo('permission-delete');
    }
}
