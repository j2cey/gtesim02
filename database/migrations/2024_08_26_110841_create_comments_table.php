<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Traits\Migrations\BaseMigrationTrait;

return new class extends Migration
{
    use BaseMigrationTrait;

    public $comments = ['table_name' =>"comments",'table_comment' => "comments list"];
    public $commentUserVotes = ['table_name' =>"comment_user_vote",'table_comment' => "user comment votes"];
    public $commentSpams = ['table_name' =>"comment_spam",'table_comment' => "comment spams"];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->comments['table_name'], function (Blueprint $table) {
            $table->id();

            $table->text('comment_text');
            $table->integer('votes')->default(0);
            $table->integer('spam')->default(0);
            $table->integer('reply_id')->default(0);
            $table->string('page_id')->default(0);

            $table->foreignId('user_id')->nullable()
                ->comment('user reference')
                ->constrained('users')->onDelete('set null');

            $table->string('commentable_type');
            $table->bigInteger('commentable_id');

            $table->baseFields();
        });
        $this->setTableComment($this->comments['table_name'],$this->comments['table_comment']);

        Schema::create($this->commentUserVotes['table_name'], function (Blueprint $table) {
            $table->id();

            $table->integer('comment_id');
            $table->string('vote',11);

            $table->foreignId('user_id')->nullable()
                ->comment('user reference')
                ->constrained('users')->onDelete('set null');

            $table->baseFields();
        });
        $this->setTableComment($this->commentUserVotes['table_name'],$this->commentUserVotes['table_comment']);

        Schema::create($this->commentSpams['table_name'], function (Blueprint $table) {
            $table->id();

            $table->integer('comment_id');
            $table->foreignId('user_id')->nullable()
                ->comment('user reference')
                ->constrained('users')->onDelete('set null');

            $table->baseFields();
        });
        $this->setTableComment($this->commentSpams['table_name'],$this->commentSpams['table_comment']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->comments['table_name'], function (Blueprint $table) {
            $table->dropBaseForeigns();
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists($this->comments['table_name']);

        Schema::table($this->commentUserVotes['table_name'], function (Blueprint $table) {
            $table->dropBaseForeigns();
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists($this->commentUserVotes['table_name']);

        Schema::table($this->commentSpams['table_name'], function (Blueprint $table) {
            $table->dropBaseForeigns();
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists($this->commentSpams['table_name']);
    }
};
