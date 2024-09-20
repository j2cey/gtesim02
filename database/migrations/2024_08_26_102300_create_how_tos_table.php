<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'how_tos';
    public $table_comment = 'how-to list';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->string('title')->comment('how-to title');
            $table->string('code')->comment('how-to code');
            $table->string('view')->nullable()->comment('how-to view');
            $table->string('description')->nullable()->comment('how-to description');
            $table->longText('htmlbody')->nullable()->comment('how-to description');

            $table->foreignId('how_to_type_id')->nullable()
                  ->comment('how_to_type reference')
                  ->constrained('how_to_types')->onDelete('set null');

              /*$table->foreignId('created_by')->nullable()
                  ->comment('user creator reference')
                  ->constrained('users')->onDelete('set null');

              $table->foreignId('updated_by')->nullable()
                  ->comment('user updator reference')
                  ->constrained('users')->onDelete('set null');*/

            $table->baseFields();
        });

    }

    /**
     * Reverse the migrations.
     *$this->setTableComment($this->table_name,$this->table_comment);
     * @return void
     */
    public function down()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropBaseForeigns();
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['how_to_type_id']);
        });
        Schema::dropIfExists($this->table_name);
    }
};
