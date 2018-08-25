<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersHolidayConfigTable extends Migration
{
    /**
     * Run the migrations.
     *  系统配置 员工 假期配置表
     * @return void
     */
    public function up()
    {
        Schema::create('users_holiday_config', function (Blueprint $table) {
            $table->increments('holiday_id');
            $table->unsignedInteger('apply_type_id')->comment = '申请类型';
            $table->string('holiday', 20)->comment = '假期明细类型名称';
            $table->text('memo')->nullable()->comment = '假期描述';
            $table->unsignedInteger('num')->defult(0)->comment = '假期天数';
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
        Schema::dropIfExists('users_holiday_config');
    }
}
