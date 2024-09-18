<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\Migrations\BaseMigrationTrait;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $table_name = 'esim_qrcodes';
    public $table_comment = 'liste de QR Codes d une e-sim donnee';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->string('raw_value')->nullable()->comment('raw_value');
            $table->string('qrcode_img')->nullable()->comment('qrcode_img');

            $table->foreignId('esim_id')->nullable()
                ->comment('reference de l e-sim')
                ->constrained('esims')->onDelete('set null');

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
