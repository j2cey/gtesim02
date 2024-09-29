<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'client_esims';
    public $table_comment = 'liste des clients.';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->string('nom_raison_sociale')->comment('nom_raison_sociale');
            $table->string('prenom')->nullable()->comment('prenom');
            $table->string('email')->comment('email');
            $table->string('numero_telephone')->comment('principal numero telephone');
            $table->string('pin')->nullable()->comment('pin');
            $table->string('puk')->nullable()->comment('puk');

            $table->foreignId('esim_id')->nullable()
                ->comment('reference de l esim')
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
            $table->dropForeign(['esim_id']);
        });
        Schema::dropIfExists($this->table_name);
    }
};
