<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewStepFlowConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_step_flow_config', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('step_id')->comment = '审核流配置ID';
            $table->unsignedInteger('step_order_id')->comment = '审核步骤ID，具体可查看模型配置';
            $table->unsignedTinyInteger('assign_type')->default(0)->comment = '指定审核类型';
            $table->unsignedInteger('assign_uid')->nullable()->comment = '指定人ID';
            $table->unsignedTinyInteger('group_type_id')->default(0)->comment = '指定组类型 0:本部门 1:不限';
            $table->unsignedInteger('assign_role_id')->nullable()->comment = '指定组，角色ID';

            $table->timestamps();

            $table->index(['step_id']);
            $table->unique(['step_id', 'step_order_id'], 'unite_step');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_step_flow_config');
    }
}
