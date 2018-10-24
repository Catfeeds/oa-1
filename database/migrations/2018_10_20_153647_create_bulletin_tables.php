<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulletinTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulletin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('send_user', '10')->comment = '发布人姓名';
            $table->string('title', 20)->comment = '公告标题';
            $table->text('content')->comment = '公告内容';
            $table->unsignedMediumInteger('valid_time')->default(1)->comment = '有效日期多少天';
            $table->unsignedInteger('weight')->default(0)->comment = '权重';
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
        Schema::dropIfExists('bulletin');
    }
}
