<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_created_by_foreign');
            $table->dropForeign('users_updated_by_foreign');

            $table->dropColumn(['created_by', 'updated_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('created_by')->nullable()
                    ->comment('user creator reference')
                    ->constrained('users')->onDelete('set null');

                $table->foreignId('updated_by')->nullable()
                    ->comment('user updator reference')
                    ->constrained('users')->onDelete('set null');
            });
        });
    }
};
