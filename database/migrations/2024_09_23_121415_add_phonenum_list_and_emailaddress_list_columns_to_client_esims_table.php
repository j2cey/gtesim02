<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table_name = 'client_esims';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->string('phone_number_list')->nullable()->comment('list of client s phone numbers');
            $table->string('email_address_list')->nullable()->comment('list of client s emailaddresses');
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
