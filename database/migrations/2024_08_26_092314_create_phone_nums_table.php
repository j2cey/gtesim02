<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\Migrations\BaseMigrationTrait;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'phone_nums';
    public $table_comment = 'Liste des numeros de telephone du Systeme';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();
            $table->baseFields();

            $table->string('phone_number')->comment('le numéro de téléphone');
            $table->integer('posi')->default(0)->comment('position du numéro de phone dans la liste de numéros.');

            $table->string('hasphonenum_type')->comment('type du modèle référencé');
            $table->bigInteger('hasphonenum_id')->comment('id du modèle référencé');
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
