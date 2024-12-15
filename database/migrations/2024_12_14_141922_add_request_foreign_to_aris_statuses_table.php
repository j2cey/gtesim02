<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public string $table_name = 'aris_statuses';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->foreignId('request_id')->nullable()
                ->comment('request reference')
                ->constrained('aris_status_requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropForeign(['request_id']);
        });
    }
};
