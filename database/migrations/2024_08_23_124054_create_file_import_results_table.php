<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\Migrations\BaseMigrationTrait;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'file_import_results';
    public $table_comment = 'import result of a given file';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->boolean('imported')->default(false)->comment('determines if the file has already been imported into the DB');
            $table->boolean('import_processing')->default(false)->comment('determines if the file is being processed');
            $table->timestamp('suspended_at')->nullable()->comment('date of suspension if applicable');

            $table->timestamp('importstart_at')->nullable()->comment('start date of import into the DB');
            $table->timestamp('importend_at')->nullable()->comment('end date of import into the DB');

            $table->integer('nb_rows')->default(0)->comment('total rows');
            $table->integer('nb_rows_success')->default(0)->comment('total number of rows successfully imported');
            $table->integer('nb_rows_failed')->default(0)->comment('total number of failed rows');
            $table->integer('nb_rows_processing')->default(0)->comment('total number of rows being processed');
            $table->integer('nb_rows_processed')->default(0)->comment('total number of rows processed');

            $table->integer('row_last_processed')->default(0)->comment('last line processed');
            $table->integer('nb_try')->default(0)->comment('number of processing attempts');
            $table->json('report')->comment('import report');

            $table->string('hasimportfileresult_type')->comment('referenced model type');
            $table->bigInteger('hasimportfileresult_id')->comment('referenced model id');

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
