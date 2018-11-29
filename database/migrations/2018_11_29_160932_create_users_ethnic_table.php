<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersEthnicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_ethnic', function (Blueprint $table) {
            $table->increments('ethnic_id');
            $table->string('ethnic', 20)->comment = '民族名称';
            $table->integer('sort')->comment = '排序';
            $table->timestamps();

            $table->unique(['ethnic']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_ethnic');
    }
}
