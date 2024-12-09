<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HowTos\HowToType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HowToTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HowToType::updateOrNew("default","the default how to type")
            ->setDefault();
    }
}
