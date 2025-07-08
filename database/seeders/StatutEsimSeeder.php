<?php

namespace Database\Seeders;

use App\Models\Esims\StatutEsim;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatutEsimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StatutEsim::updateOrNew("Nouveau", "nouveau", "nouveau", "default")->setDefault();
        StatutEsim::updateOrNew("Attribution", "attribution", "attribution", "warning");
        StatutEsim::updateOrNew("Attribue", "attribue", "attribue", "success");
        StatutEsim::updateOrNew("Suspendue", "suspendue", "suspendue", "danger");
        StatutEsim::updateOrNew("Reservee", "reservee", "reservee", "warning");
        StatutEsim::updateOrNew("Stock", "stock", "en stock", "info");
    }
}
