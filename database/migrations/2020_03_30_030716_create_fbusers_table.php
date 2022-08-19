<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fbusers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fburl')->unique();
            $table->string('name')->nullable();
            $table->boolean('is_like_fanpage')->default(0);
            $table->boolean('is_eligible')->default(0);
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
        Schema::dropIfExists('fbusers');
    }
}