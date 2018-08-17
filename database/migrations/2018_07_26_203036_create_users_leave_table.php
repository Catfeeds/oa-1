<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *  员工 申请假期表
     * @return void
     */
    public function up()
    {
        Schema::create('users_leave', function (Blueprint $table) {
            $table->increments('leave_id');
            $table->unsignedInteger('user_id')->comment = '用户唯一ID';
            $table->unsignedInteger('apply_type_id')->comment = '申请类型';
            $table->unsignedInteger('holiday_id')->comment = '假期类型ID';
            $table->unsignedInteger('step_id')->comment = '审批步骤ID';
            $table->timestamp('start_time')->nullable()->comment = '开始日期';
            $table->unsignedInteger('start_id')->nullable()->comment = '开始时间ID';
            $table->timestamp('end_time')->nullable()->comment = '结束日期';
            $table->unsignedInteger('end_id')->nullable()->comment = '结束时间ID';
            $table->text('reason')->comment = '申请理由';
            $table->text('user_list')->nullable()->comment = '批量申请员工列表';
            $table->unsignedInteger('status')->defult(0)->comment = '审批状态 0:待审批 1:审批通过 2:审批未通过 具体查看配置';
            $table->string('annex', 50)->nullable()->comment = '附件地址';
            $table->unsignedInteger('review_user_id')->nullable()->comment = '当前审核人ID';
            $table->text('remain_user')->nullable()->comment = '剩下顺序审批人员信息';

            $table->index('user_id');
            $table->index('holiday_id');
            $table->index('status');
            $table->index('start_time');
            $table->index('end_time');

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
        Schema::dropIfExists('users_leave');
    }
}
