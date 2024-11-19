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
        $data_uniq = ['name' => "admin",'login' => "admin",'email' => "admin@gtesim.com", 'is_local' => true];
        $pwd = bcrypt( config('app.admin_password') );
        $data = ['local_password' => $pwd, 'password' => $pwd, 'status_id' => Status::active()->first()->id];
        //$user_admin = User::upsert($data, uniqueBy: $data, update: $data);
        $user_admin = User::firstOrCreate ($data_uniq, $data);

        /*$data_uniq = ['name' => "John DOE",'login' => "simple",'email' => "simple@sheundani.net", 'is_local' => true];
        $pwd = bcrypt( config('app.admin_password') );
        $data = ['local_password' => $pwd, 'password' => $pwd, 'status_id' => Status::active()->first()->id];
        //$user_simple = User::upsert($data, uniqueBy: $data, update: $data);
        $user_simple = User::firstOrCreate ($data_uniq, $data);*/

        $user_admin->assignRole([Role::where('name', "Admin")->first()->id]);
        //$user_simple->assignRole([Role::where('name', "Simple UserResource")->first()->id]);
    }
}
