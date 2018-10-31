<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewStepFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_step_flow', function (Blueprint $table) {
            $table->increments('step_id');
            $table->unsignedInteger('apply_type_id')->comment = '项目类型 1:请假 2:调休 3:打卡 具体可查看模型配置';
            $table->unsignedInteger('child_id')->nullable()->comment = '项目子类型 具体可查看模型配置';
            $table->decimal('min_num', 12, 1)->nullable()->comment= '限制条件最小值';
            $table->decimal('max_num', 12, 1)->nullable()->comment= '限制条件最大值';
            $table->unsignedTinyInteger('is_modify')->default(0)->comment = '是否允许修改审批人 0:否 1:是';

            $table->timestamps();

            $table->index(['apply_type_id', 'child_id'], 'apply_child_id');
            $table->index(['min_num', 'max_num'], 'num_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_step_flow');
    }
}
