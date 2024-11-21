<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public string $table_name = 'aris_status_requests';
    public string $table_comment = 'Esim s ARIS Status Requests';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->integer('min_esim_id')->nullable()->comment('min esim id requested');
            $table->integer('last_requested_esim_id')->default(0)->comment('last requested esim id for this Request');
            $table->integer('max_esim_id')->nullable()->comment('max esim id requested');
            $table->integer('request_status')->default(0)->comment('request status: 0 -> not done; 2 -> running; 1 -> done; < 0 -> ERROR');
            $table->integer('last_response_code')->nullable()->comment('last response code');
            $table->string('request_message', 500)->nullable()->comment('request message');

            $table->timestamp('start_at')->nullable()->comment('request start date');
            $table->timestamp('end_at')->nullable()->comment('request end date');

            $table->timestamps();
        });
        $this->setTableComment($this->table_name,$this->table_comment);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table_name);
    }
};
