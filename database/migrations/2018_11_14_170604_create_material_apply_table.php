<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_apply', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment = '借用的用户id';
            $table->text('reason')->comment = '借用事由';
            $table->dateTime('expect_return_time')->comment = '预计归还时间';
            $table->string('annex')->comment = '附件地址';
            $table->unsignedSmallInteger('state')->default(0)->comment = '状态';
            $table->integer('step_id')->comment = '审批步骤ID';
            $table->integer('review_user_id')->comment = '当前审核人ID';
            $table->text('remain_user')->comment = '剩下顺序审批人员信息';
            $table->text('step_user')->comment = '审批步骤用户ID';
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
        Schema::dropIfExists('material_apply');
    }
}
