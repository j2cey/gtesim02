<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'esim_states';
    public $table_comment = 'all the differents status variations of a given eSIM';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->foreignId('esim_id')->nullable()
                ->comment('reference de l e-sim')
                ->constrained('esims')->onDelete('set null');

            $table->foreignId('statut_esim_id')->nullable()
                ->comment('reference du statut de l e-sim')
                ->constrained('statut_esims')->onDelete('set null');

            $table->foreignId('user_id')->nullable()
                ->comment('user reference')
                ->constrained('users')->onDelete('set null');

            $table->string('details', 500)->nullable()->comment('variation details');

            $table->baseFields();
            $table->softDeletes();
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
            $table->dropForeign(['esim_id']);
            $table->dropForeign(['statut_esim_id']);
            $table->dropForeign(['user_id']);

            $table->dropSoftDeletes();
        });
        Schema::dropIfExists($this->table_name);
    }
};
