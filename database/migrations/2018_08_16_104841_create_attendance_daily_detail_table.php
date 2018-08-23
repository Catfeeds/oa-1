<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceDailyDetailTable extends Migration
{
    /**
     * Run the migrations.
     *  员工每日考勤表
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_daily_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment = '用户唯一ID';
            $table->date('day')->comment = '打卡日期';
            $table->string('punch_start_time', 10)->nullable()->comment = '当天打卡时间';
            $table->unsignedInteger('punch_start_time_num')->nullable()->comment = '当天打卡时间，冗余字段用来比较时间';
            $table->string('punch_end_time', 10)->nullable()->comment = '当天结束时间';
            $table->unsignedInteger('punch_end_time_num')->nullable()->comment = '当天结束时间，冗余字段用来比较时间';
            $table->unsignedInteger('heap_late_num')->defult(0)->comment = '累积迟到分钟数';
            $table->unsignedInteger('lave_buffer_num')->defult(0)->comment = '剩余缓冲分钟数';
            $table->unsignedInteger('deduction_num')->defult(0)->comment = '扣分';
            $table->timestamps();

            $table->unique(['day', 'user_id'], 'unique_user_daily');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_daily_detail');
    }
}
