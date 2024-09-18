<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


return new class extends Migration
{
    public $exclude_tables = ["migrations","failed_jobs","ldap_account_imports","password_resets","jobs","audits","tags","taggables"];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $columns = 'Tables_in_' . env('DB_DATABASE');//This is just to read the object by its key, DB_DATABASE is database name.
        $tables = DB::select('SHOW TABLES');

        foreach ( $tables as $table ) {
            if ( ! in_array($table->$columns, $this->exclude_tables)) {
                $this->setSoftDeletes($table->$columns);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $columns = 'Tables_in_' . env('DB_DATABASE');//This is just to read the object by its key, DB_DATABASE is database name.
        $tables = DB::select('SHOW TABLES');

        foreach ( $tables as $table ) {
            if ( ! in_array($table->$columns, $this->exclude_tables)) {
                $this->unsetSoftDeletes($table->$columns);
            }
        }
    }

    private function setSoftDeletes($table_name) {
        Schema::table($table_name, function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    private function unsetSoftDeletes($table_name) {
        Schema::table($table_name, function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
