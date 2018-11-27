<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldAttendanceDailyDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_daily_detail', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->default(0)->comment = '打卡生成状态 默认0 0:待打卡信息导入 1:导入完成 2:导入失败';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_daily_detail', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
