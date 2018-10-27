<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunchRulesConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punch_rules_config', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('punch_rules_id')->comment = '上下班配置ID';
            $table->string('ready_time', 20)->nullable()->comment = '上班准备时间';
            $table->string('work_start_time', 20)->nullable()->comment = '上班时间';
            $table->string('work_end_time', 20)->nullable()->comment = '下班时间';
            $table->string('rule_desc', 20)->nullable()->comment = '规则描述';
            $table->unsignedTinyInteger('late_type')->nullable()->comment = '上班类型 1:上班迟到 2:下班早退 ,具体查看数据库模型配置';
            $table->string('start_gap', 20)->nullable()->comment = '范围起始扣分规则';
            $table->string('end_gap', 20)->nullable()->comment = '范围结束扣分规则';
            $table->unsignedTinyInteger('ded_type')->nullable()->comment = '扣分类型 1:分数类型 2:假期配置类型 ,具体查看数据库模型配置';
            $table->unsignedInteger('holiday_id')->nullable()->comment = '假期配置ID';
            $table->unsignedInteger('ded_num')->nullable()->comment = '扣分单位';

            $table->index('punch_rules_id');
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
        Schema::dropIfExists('punch_rules_config');
    }
}
