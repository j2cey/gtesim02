<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\Migrations\BaseMigrationTrait;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'email_addresses';
    public $table_comment = 'Liste des adresses e-mail du Systeme';

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

            $table->string('email')->comment('l adresse e-mail');
            $table->integer('posi')->default(0)->comment('position de l adresse e-mail dans la liste d adresses.');

            $table->string('hasemailaddress_type')->comment('type du modèle référencé');
            $table->bigInteger('hasemailaddress_id')->comment('id du modèle référencé');

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
