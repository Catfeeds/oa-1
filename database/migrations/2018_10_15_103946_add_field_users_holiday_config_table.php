<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldUsersHolidayConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_holiday_config', function (Blueprint $table) {
            $table->string('show_name', 32)->after('holiday')->comment = '事件显示名称';
            $table->unsignedInteger('cypher_type')->comment = '计算类型,具体类型可查看数据库模型配置';
            $table->string('work_relief_formula', 32)->nullable()->comment = '上下班时间减免公式, {年,月,日,时,分,秒}';
            $table->unsignedInteger('work_relief_type')->nullable()->comment = '上下班时间减免类型，具体查看数据库模型配置';
            $table->unsignedInteger('work_relief_cycle_num')->nullable()->comment = '上下班时间减免循环周期次数';
            $table->unsignedInteger('add_pop')->nullable()->comment = '调休假期增加比例';
            $table->decimal('up_day', 8 , 1)->nullable()->comment = '请假上限天数';
            $table->decimal('under_day', 8 , 1)->nullable()->comment = '请假下限天数';
            $table->unsignedInteger('cycle_num')->nullable()->comment = '周期内可请假次数';
            $table->unsignedInteger('payable')->nullable()->comment = '计薪比例';
            $table->string('payable_reset_formula', 32)->nullable()->comment = '计薪天数重置周期公式, {年,月,日,时,分,秒}';
            $table->string('payable_claim_formula', 32)->nullable()->comment = '计薪天数起始要求, {年,月,日,时,分,秒}';
            $table->unsignedInteger('payable_self_growth')->nullable()->comment = '计薪天数自增长';
            $table->unsignedInteger('exceed_change_id')->nullable()->comment = '超出天数转换';
            $table->unsignedTinyInteger('is_show')->default(1)->comment = '是否显示事件,默认0，0:否 1:是 ,具体查看数据库模型配置';
            $table->unsignedTinyInteger('is_before_after')->default(0)->comment = '是否允许节假日前后申请,默认0，0:否 1: 是,具体查看数据库模型配置';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_holiday_config', function (Blueprint $table) {
            $table->dropColumn('show_name');
            $table->dropColumn('cypher_type');
            $table->dropColumn('work_relief_formula');
            $table->dropColumn('work_relief_type');
            $table->dropColumn('work_relief_cycle_num');
            $table->dropColumn('add_pop');
            $table->dropColumn('up_day');
            $table->dropColumn('under_day');
            $table->dropColumn('cycle_num');
            $table->dropColumn('payable');
            $table->dropColumn('payable_reset_formula');
            $table->dropColumn('payable_claim_formula');
            $table->dropColumn('payable_self_growth');
            $table->dropColumn('exceed_change_id');
            $table->dropColumn('is_show');
            $table->dropColumn('is_before_after');
        });
    }
}
