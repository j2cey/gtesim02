<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public string $table_name = 'aris_status_requests';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->integer('prev_request_status')->default(0)->comment('previous request status: 0 -> not done; 2 -> running; 1 -> done; < 0 -> ERROR');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            if (Schema::hasColumn($this->table_name, 'prev_request_status')) {
                $table->dropColumn('prev_request_status');
            }
        });
    }
};
