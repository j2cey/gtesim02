<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EditAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //$user = User::where('login', "admin")->first();
        $user = User::find(1);
        $user->password = bcrypt( config('app.admin_password') );
        $user->name = "admin";
        $user->email = "admin@sheundani.net";
        $user->save();
    }
}
