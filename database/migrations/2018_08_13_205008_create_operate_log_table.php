<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperateLogTable extends Migration
{
    /**
     * Run the migrations.
     *  审核操作日志表
     * @return void
     */
    public function up()
    {
        Schema::create('operate_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('type_id')->comment = '类型ID 1: 假期申请模块';
            $table->unsignedInteger('info_id')->comment = '对应操作信息ID';
            $table->unsignedInteger('opt_uid')->comment = '操作者用户ID';
            $table->string('opt_name', 200)->comment = '操作类型名称';
            $table->text('memo')->nullable()->comment = '备注信息';

            $table->unique(['id', 'type_id', 'info_id', 'opt_uid'], 'operate_unique');
            $table->index(['opt_uid', 'opt_name'], 'opt_uid_name');
            $table->index('type_id');
            $table->index('info_id');

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
        Schema::dropIfExists('operate_log');
    }
}
