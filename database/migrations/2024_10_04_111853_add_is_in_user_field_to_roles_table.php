<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->boolean('is_in_user')->default(false)->comment('tell if role is in specified user, when listing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropColumn('is_in_user');
        });
    }
};
