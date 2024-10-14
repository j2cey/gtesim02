<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public string $table_name = 'employes';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->string('phone_number_list')->nullable()->comment('list of employee s phone numbers');
            $table->string('email_address_list')->nullable()->comment('list of employee s emailaddresses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropColumn('phone_number_list');
            $table->dropColumn('email_address_list');
        });
    }
};
