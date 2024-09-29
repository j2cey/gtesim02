<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public $table_name = 'client_esims';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->index(['nom_raison_sociale', 'prenom']);
            $table->index(['phone_number_list', 'email_address_list']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropIndex(['nom_raison_sociale', 'prenom']);
            $table->dropIndex(['phone_number_list', 'email_address_list']);
        });
    }
};
