<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public string $table_name = 'esim_states';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->integer('ord')->default(0)->comment('numero ordre');
            $table->foreignId('prev_esim_state_id')->nullable()
                ->comment('reference du précédent statut de l e-sim')
                ->constrained('esim_states')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropColumn('ord');
            $table->dropForeign(['prev_esim_state_id']);
        });
    }
};
