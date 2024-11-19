<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public string $table_name = 'ldap_users';
    public string $table_comment = 'Users load from LDAP';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->id();

            $table->string('guid')->nullable()->comment('LDAP ref');
            $table->string('name')->nullable()->comment('user full name');
            $table->string('firstname')->nullable()->comment('user full first name');
            $table->string('lastname')->nullable()->comment('user full last name');
            $table->string('login')->unique()->nullable()->comment('user login');
            $table->string('email')->unique();
            $table->string('domain')->nullable()->comment('LDAP domain');
            $table->string('telephone')->nullable()->comment('telephonenumber');
            $table->string('distinguishedname')->nullable()->comment('distinguishedname');
            $table->string('department_tree')->nullable()->comment('department_tree');
            $table->string('title')->nullable()->comment('title');
            $table->string('password');

            $table->foreignId('fonction_employe_id')->nullable()
                ->comment('reference de la fonction de l employe')
                ->constrained('fonction_employes')->onDelete('set null');

            $table->foreignId('departement_id')->nullable()
                ->comment('reference du departement d affectation de l employe (le cas echeant)')
                ->constrained('departements')->onDelete('set null');

            $table->timestamps();
        });
        $this->setTableComment($this->table_name,$this->table_comment);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('ldap_user_id')->nullable()
                ->comment('ldap account reference')
                ->constrained($this->table_name)->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropForeign(['fonction_employe_id']);
            $table->dropForeign(['departement_id']);
        });
        Schema::dropIfExists($this->table_name);
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropForeign(['ldap_user_id']);
        });
    }
};
