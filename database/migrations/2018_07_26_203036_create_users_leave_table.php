<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_leave', function (Blueprint $table) {
            $table->increments('leave_id');
            $table->unsignedInteger('user_id')->comment = '用户唯一ID';
            $table->unsignedInteger('apply_type')->comment = '申请类型';
            $table->unsignedInteger('type')->comment = '请假类型';
            $table->timestamp('start_time')->nullable()->comment = '开始日期';
            $table->unsignedInteger('start_id')->nullable()->comment = '开始时间ID';
            $table->timestamp('end_time')->nullable()->comment = '结束日期';
            $table->unsignedInteger('end_id')->nullable()->comment = '结束时间ID';
            $table->text('reason')->comment = '请假理由';
            $table->text('user_list')->nullable()->comment = '批量申请员工列表';
            $table->unsignedInteger('status')->defult(0)->comment = '审批状态 0:未审批 1:未通过 具体查看配置';
            $table->string('annex', 50)->nullable()->comment = '附件地址';

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
