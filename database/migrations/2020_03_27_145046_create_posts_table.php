<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid("id")->primary();;
            $table->string("post_id")->nullable()->unique();
            $table->integer("like")->default(0);
            $table->integer("love")->default(0);
            $table->integer("haha")->default(0);
            $table->integer("wow")->default(0);
            $table->integer("sad")->default(0);
            $table->integer("care")->default(0);
            $table->integer("angry")->default(0);
            $table->float("point")->default(0);
            $table->text("avatar")->nullable();
            $table->integer("ranking")->nullable();
            $table->string("title")->nullable();
            $table->integer("shared")->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
