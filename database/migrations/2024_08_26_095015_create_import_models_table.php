<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\Migrations\BaseMigrationTrait;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'import_models';
    public $table_comment = 'import models list.';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable()->comment('title of the import model');
            $table->string('code')->nullable()->comment('code of the import model');

            $table->string('filemodel_type')->nullable()->comment('file model of the import model');
            $table->string('targetmodel_type')->nullable()->comment('target model of the import model');

            $table->string('array_values')->nullable()->comment('array values the import model');
            $table->string('file_fullname')->nullable()->comment('full name of current importing file');
            $table->string('description')->nullable()->comment('import model description');

            $table->string('hasimportmodel_type')->comment('referenced model to import (class name)');
            $table->bigInteger('hasimportmodel_id')->comment('referenced model to import id (object id)');

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
        });
        Schema::dropIfExists($this->table_name);
    }
};
