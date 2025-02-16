<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public string $table_name = 'aris_status_request_stats';
    public string $table_comment = 'Esim s ARIS Status Request Statistics';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->integer('requests_to_send_count')->default(0)->comment('number of requests to send');
            $table->integer('waiting_requests_count')->default(0)->comment('number of waiting requests (with waiting status code)');

            $table->integer('requests_sent_count')->default(0)->comment('number of sent requests');
            $table->integer('requests_sent_rate')->default(0)->comment('rate of sent requests');

            $table->integer('requests_waiting_result_count')->default(0)->comment('number of requests sent and waiting result');
            $table->integer('requests_waiting_result_rate')->default(0)->comment('rate of requests sent and waiting result');

            $table->integer('requests_received_count')->default(0)->comment('number of received (answered) requests');
            $table->integer('requests_received_rate')->default(0)->comment('rate of received (answered) requests');

            $table->integer('requests_queueing_count')->default(0)->comment('number of queueing requests');
            $table->integer('requests_queueing_rate')->default(0)->comment('rate of queueing requests');

            $table->integer('requests_busy_count')->default(0)->comment('number of busy requests');
            $table->integer('requests_busy_rate')->default(0)->comment('rate of busy requests');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table_name);
    }
};
