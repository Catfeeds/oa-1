<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppealTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appeal', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedMediumInteger('appeal_type')->comment = "申诉类型: 1为请假类型 2为每日明细类型";
            $table->unsignedInteger('user_id')->comment = "申诉用户id";

            $table->unsignedInteger('apply_type_id')->nullable()->comment = "若请假申诉,申诉的请假类型";
            $table->unsignedInteger('leave_id')->nullable()->comment = "若请假申诉,申诉的假期id";

            $table->unsignedInteger('daily_id')->nullable()->comment = "若明细申诉,申诉的每日明细id";

            $table->string('reason', 50)->comment = "申诉理由";
            $table->unsignedSmallInteger('result')->nullable()->comment = "申诉状态: 0为未审核 1为接收申诉 2为拒绝申诉";
            $table->string('remark', 50)->nullable()->comment = "审核人员填写的备注";
            $table->unsignedInteger('operate_user_id')->nullable()->comment = "操作的管理员用户id";
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
        Schema::dropIfExists('appeal');
    }
}
