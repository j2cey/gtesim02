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
            $table->integer('requests_to_send_count')->default(0)->comment('number of requests to send');
            $table->integer('requests_sent_count')->default(0)->comment('number of sent requests');
            $table->integer('requests_sent_rate')->default(0)->comment('rate of sent requests');

            $table->integer('requests_waiting_result_count')->default(0)->comment('number of requests waiting to response');
            $table->integer('requests_waiting_result_rate')->default(0)->comment('rate of requests waiting to response');

            $table->integer('requests_received_count')->default(0)->comment('number of received requests');
            $table->integer('requests_received_rate')->default(0)->comment('rate of received requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            if (Schema::hasColumn($this->table_name, 'requests_to_send_count')) {
                $table->dropColumn('requests_to_send_count');
            }
            if (Schema::hasColumn($this->table_name, 'requests_sent_count')) {
                $table->dropColumn('requests_sent_count');
            }
            if (Schema::hasColumn($this->table_name, 'requests_sent_rate')) {
                $table->dropColumn('requests_sent_rate');
            }

            if (Schema::hasColumn($this->table_name, 'requests_waiting_result_count')) {
                $table->dropColumn('requests_waiting_result_count');
            }
            if (Schema::hasColumn($this->table_name, 'requests_waiting_result_rate')) {
                $table->dropColumn('requests_waiting_result_rate');
            }

            if (Schema::hasColumn($this->table_name, 'requests_received_count')) {
                $table->dropColumn('requests_received_count');
            }
            if (Schema::hasColumn($this->table_name, 'requests_received_rate')) {
                $table->dropColumn('requests_received_rate');
            }
        });
    }
};
