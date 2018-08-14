<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmProductTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmr_product', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment = '游戏ID';
            $table->string('name',32)->comment = '游戏名';

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
        Schema::dropIfExists('cmr_product');
    }
}
