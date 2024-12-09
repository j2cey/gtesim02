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
        Schema::table('how_tos', function (Blueprint $table) {
            $table->longText('htmlbody_parsed')->nullable()->comment('how-to html body parsed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('how_tos', function (Blueprint $table) {
            $table->dropColumn('htmlbody_parsed');
        });
    }
};
