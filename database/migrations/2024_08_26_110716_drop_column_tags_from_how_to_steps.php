<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('how_to_steps', function (Blueprint $table) {
            if (Schema::hasColumn('how_to_steps', 'tags')) {
                $table->dropColumn(['tags']);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('how_to_steps', function (Blueprint $table) {
            $table->string('tags')->nullable()->comment('Tags, if any');
        });
    }
};
