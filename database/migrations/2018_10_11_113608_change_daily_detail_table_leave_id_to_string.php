<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDailyDetailTableLeaveIdToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_daily_detail', function (Blueprint $table) {
            $table->string('leave_id', 50)->nullable()->change()->comment = '假期申请单ID，默认为空';
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
            $table->unsignedInteger('leave_id')->nullable()->change()->comment = '假期申请单ID，默认为空';
        });
    }
}
