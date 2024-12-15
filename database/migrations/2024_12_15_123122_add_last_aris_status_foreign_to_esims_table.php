<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public string $table_name = 'esims';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->foreignId('last_aris_status_id')->nullable()
                ->comment('aris status reference')
                ->constrained('aris_statuses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropForeign(['last_aris_status_id']);
        });
    }
};
