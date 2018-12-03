<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFieldEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entry', function (Blueprint $table) {
            $table->string('salary_card', 20)->nullable()->change()->comment = '工资卡';
            $table->text('family_num')->nullable()->change()->comment = '家庭成员';
            $table->text('work_history')->nullable()->change()->comment = '工作经历';
            $table->text('project_empiric')->nullable()->change()->comment = '项目经验';
            $table->text('awards')->nullable()->change()->comment = '获奖情况';
            $table->string('used_email')->nullable()->change()->comment = '常有邮箱';
            $table->unsignedInteger('friend_id')->nullable()->change()->comment = '基友ID';

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
