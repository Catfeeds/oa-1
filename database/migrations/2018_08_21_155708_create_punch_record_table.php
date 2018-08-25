<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunchRecordTable extends Migration
{
    /**
     * Run the migrations.
     * 打卡导入保存
     * @return void
     */
    public function up()
    {
        Schema::create('punch_record', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->comment = '附件保存名称';
            $table->string('annex', 200)->nullable()->comment = '附件地址';
            $table->unsignedInteger('status')->defult(0)->comment = '生成状态 0:未生成 1:生成中 2:生成失败 3:生成完成';
            $table->string('log_file', 200)->nullable()->comment = '生成信息日志地址';
            $table->timestamps();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('punch_record');
    }
}
