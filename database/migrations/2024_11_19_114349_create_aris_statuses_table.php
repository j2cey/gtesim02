<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public string $table_name = 'aris_statuses';
    public string $table_comment = 'Esim s ARIS Statuses';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->string('icc')->nullable()->comment('icc from ARIS');
            $table->string('status')->nullable()->comment('status from ARIS');
            $table->timestamp('status_change_date')->nullable()->comment('status change date from ARIS');

            $table->timestamp('requested_at')->nullable()->comment('request date');
            $table->timestamp('responded_at')->nullable()->comment('response date');
            $table->string('response_message', 500)->nullable()->comment('response message');

            $table->foreignId('esim_id')->nullable()
                ->comment('esim reference')
                ->constrained('esims')->onDelete('set null');

            $table->timestamps();
        });
        $this->setTableComment($this->table_name,$this->table_comment);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropForeign(['esim_id']);
        });
        Schema::dropIfExists($this->table_name);
    }
};
