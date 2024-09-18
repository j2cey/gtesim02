<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'how_to_steps';
    public $table_comment = 'list of steps (articulations) of a given how-to-thread (parent)';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->foreignId('how_to_thread_id')->nullable()
                ->comment('how_to_thread reference')
                ->constrained('how_to_threads')->onDelete('set null');

            $table->integer('posi')->default(1)->comment('step position in children list');
            $table->string('title')->nullable()->comment('step title');
            $table->string('description')->nullable()->comment('step description');

            $table->foreignId('how_to_id')->nullable()
                ->comment('how_to child reference')
                ->constrained('how_tos')->onDelete('set null');

            /*$table->foreignId('created_by')->nullable()
                ->comment('user creator reference')
                ->constrained('users')->onDelete('set null');

            $table->foreignId('updated_by')->nullable()
                ->comment('user updator reference')
                ->constrained('users')->onDelete('set null');*/

            $table->baseFields();
        });
        $this->setTableComment($this->table_name,$this->table_comment);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropBaseForeigns();
            $table->dropForeign(['how_to_thread_id']);
            $table->dropForeign(['how_to_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists($this->table_name);
    }
};
