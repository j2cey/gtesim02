<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\Migrations\BaseMigrationTrait;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'import_model_fields';
    public $table_comment = 'import model fields list.';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable()->comment('title');
            $table->string('mapped_field')->comment('field to map from Main Model');
            $table->string('description')->nullable()->comment('description');

            $table->string('innerfieldtype_type')->comment('referenced inner model (class name)');
            $table->bigInteger('innerfieldtype_id')->comment('referenced inner model id (object id)');

            $table->foreignId('import_model_id')->nullable()
                ->comment('import model reference')
                ->constrained()->onDelete('set null');

            $table->foreignId('import_model_field_type_id')->nullable()
                ->comment('import model field type reference')
                ->constrained()->onDelete('set null');

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
            $table->dropForeign(['import_model_id']);
            $table->dropForeign(['import_model_field_type_id']);
        });
        Schema::dropIfExists($this->table_name);
    }
};
