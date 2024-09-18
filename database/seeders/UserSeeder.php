<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Status;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user_admin = User::create(['name' => "admin",'login' => "admin",'email' => "admin@sheundani.net",'password' => bcrypt( config('app.admin_password') ), 'status_id' => Status::active()->first()->id]);
        $user_simple = User::create(['name' => "John DOE",'login' => "simple",'email' => "simple@sheundani.net",'password' => bcrypt( config('app.admin_password') ), 'status_id' => Status::active()->first()->id]);

        $user_admin->assignRole([Role::find(1)->id]);
        $user_simple->assignRole([Role::find(2)->id]);
    }
}
