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
            $table->renameColumn('email', 'email_address');
            $table->renameColumn('numero_telephone', 'phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->renameColumn('email_address', 'email');
            $table->renameColumn('phone_number', 'numero_telephone');
        });
    }
};
