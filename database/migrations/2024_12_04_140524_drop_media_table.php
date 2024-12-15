<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public string $table_name = 'media';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists($this->table_name);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
