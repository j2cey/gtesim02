<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'how_to_types';
    public $table_comment = 'how-to types';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->string('title')->comment('type title');
            $table->string('description')->nullable()->comment('type description');
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
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists($this->table_name);
    }
};
