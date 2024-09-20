<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->baseFields();
            $table->string('name');
            $table->string('login')->nullable()->comment('user login');
            $table->string('email')->unique();
            $table->string('username');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('image')->nullable()->comment('Avatar de l utilisateur');
            $table->boolean('is_local')->default(false)->comment('indique si le compte est locale');
            $table->boolean('is_ldap')->default(false)->comment('indique si le compte est LDAP');
            $table->string('objectguid')->nullable()->comment('GUID du compte');

            $table->string('login_type')->default("local")->comment('type de connexion');

            $table->timestamp('last_seen')->nullable()->comment('if user login then it will update last_seen time and add key for online in cache');

            $table->rememberToken();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropBaseForeigns();
        });
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
