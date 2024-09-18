<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table_name = 'esims';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->foreignId('attributed_by')->nullable()
                ->comment('(last) user attributor reference')
                ->constrained('users')->onDelete('set null');

            $table->timestamp('attributed_at')->comment('(last) attribution date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esims', function (Blueprint $table) {
            $table->dropForeign(['attributed_by']);
            $table->dropColumn(['attributed_at']);
        });
    }
};
