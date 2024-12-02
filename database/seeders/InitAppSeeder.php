<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // mysql -u rootdev -p gtesimdb < all_db_backup_20241023_0925.sql
        // php artisan db:seed
        //
        // php artisan ldap:import ldap_users --no-interaction
        // php artisan user:guid-updatefromoldldap
        // php artisan clientesim:phonenumlist-set && php artisan clientesim:emailaddresslist-set && php artisan employe:phonenumlist-set && php artisan employe:emailaddresslist-set
    }
}
