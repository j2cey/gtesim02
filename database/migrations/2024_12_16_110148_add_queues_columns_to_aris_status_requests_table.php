<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public string $table_name = 'aris_status_requests';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->timestamp('last_queueing_start_at')->nullable()->comment('request last queueing start date');
            $table->timestamp('last_queueing_end_at')->nullable()->comment('request last queueing end date');
            $table->string('last_queueing_job_id')->nullable()->comment('request last queueing job id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            if (Schema::hasColumn($this->table_name, 'last_queueing_start_at')) {
                $table->dropColumn('last_queueing_start_at');
            }
            if (Schema::hasColumn($this->table_name, 'last_queueing_end_at')) {
                $table->dropColumn('last_queueing_end_at');
            }
            if (Schema::hasColumn($this->table_name, 'last_queueing_job_id')) {
                $table->dropColumn('last_queueing_job_id');
            }
        });
    }
};
