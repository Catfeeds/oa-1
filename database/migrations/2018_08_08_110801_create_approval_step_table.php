<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalStepTable extends Migration
{
    /**
     * Run the migrations.
     *  系统配置 审核步骤配置表
     * @return void
     */
    public function up()
    {
        Schema::create('approval_step', function (Blueprint $table) {
            $table->increments('step_id');
            $table->string('name', 20)->comment = '步骤名称';
            $table->string('step', 20)->comment = '审批步骤';
            $table->decimal('min_day', 8, 1)->default(0)->comment = '请假天数最小天数限制条件';
            $table->decimal('max_day', 8, 1)->default(0)->comment = '请假天数最大天数限制条件';
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
        Schema::dropIfExists('approval_step');
    }
}
