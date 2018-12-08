<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddField1UsersExtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_ext', function (Blueprint $table) {
            $table->text('family_num')->change()->comment = '家庭成员';
            $table->timestamp('birthday')->nullable()->comment = '生日';
            $table->text('work_history')->nullable()->comment = '工作经历';
            $table->text('project_empiric')->nullable()->comment = '项目经验';
            $table->text('awards')->nullable()->comment = '获奖情况';
            $table->unsignedTinyInteger('birthday_type')->default(0)->comment = '生日类型 默认0 0:公历 1:农历';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_ext', function (Blueprint $table) {
            $table->dropColumn('birthday');
            $table->dropColumn('birthday_type');
            $table->dropColumn('work_history');
            $table->dropColumn('project_empiric');
            $table->dropColumn('awards');
        });
    }
}
