<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entry', function (Blueprint $table) {
            $table->timestamp('birthday')->nullable()->comment = '生日';
            $table->string('salary_card', 20)->comment = '工资卡';
            $table->text('family_num')->change()->comment = '家庭成员';
            $table->text('work_history')->comment = '工作经历';
            $table->text('project_empiric')->comment = '项目经验';
            $table->text('awards')->comment = '获奖情况';
            $table->string('used_email')->comment = '常有邮箱';
            $table->unsignedTinyInteger('birthday_type')->default(0)->comment = '生日类型 默认0 0:公历 1:农历';
            $table->unsignedTinyInteger('firm_call')->default(0)->comment = '是否公司挂靠 0:否 1:是';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entry', function (Blueprint $table) {
            $table->dropColumn('birthday');
            $table->dropColumn('salary_card');
            $table->dropColumn('birthday_type');
            $table->dropColumn('work_history');
            $table->dropColumn('project_empiric');
            $table->dropColumn('awards');
            $table->dropColumn('firm_call');
            $table->dropColumn('used_email');
            $table->integer('family_num')->nullable()->change()->comment = '家庭成员';
        });
    }
}
