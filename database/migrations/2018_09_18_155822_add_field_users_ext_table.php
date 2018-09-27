<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldUsersExtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_ext', function (Blueprint $table) {
            $table->dropColumn('is_confirm');
            $table->string('height', 3)->nullable()->comment = '身高';
            $table->string('weight', 3)->nullable()->comment = '体重';
            $table->string('specialty', 20)->nullable()->comment = '专业';
            $table->string('degree', 20)->nullable()->comment = '学位';
            $table->integer('genus_id')->nullable()->comment = '属相类型ID,具体看模型里面的配置';
            $table->string('political', 20)->nullable()->comment = '政治面貌';
            $table->string('job_name', 32)->nullable()->comment = '岗位名称';
            $table->unsignedInteger('leader_id')->nullable()->comment = '直属上级ID';
            $table->unsignedInteger('tutor_id')->nullable()->comment = '导师ID';
            $table->unsignedInteger('friend_id')->nullable()->comment = '基友ID';
            $table->unsignedTinyInteger('nature_id')->default(0)->comment = '工作性质,具体看模型里面的配置';
            $table->unsignedTinyInteger('hire_id')->default(0)->comment = '招聘类型,具体看模型里面的配置';
            $table->unsignedInteger('firm_id')->nullable()->comment = '所属公司ID';
            $table->string('place', 32)->nullable()->comment = '工作位置';
            $table->string('urgent_bind', 20)->nullable()->comment = '紧急联系人关系';
            $table->string('ethnic', 32)->nullable()->comment = '民族';

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
            $table->dropColumn('height');
            $table->dropColumn('weight');
            $table->dropColumn('specialty');
            $table->dropColumn('degree');
            $table->dropColumn('genus_id');
            $table->dropColumn('political');
            $table->dropColumn('job_name');
            $table->dropColumn('leader_id');
            $table->dropColumn('tutor_id');
            $table->dropColumn('friend_id');
            $table->dropColumn('nature_id');
            $table->dropColumn('hire_id');
            $table->dropColumn('firm_id');
            $table->dropColumn('place');
            $table->dropColumn('urgent_bind');
            $table->dropColumn('ethnic');
            $table->unsignedTinyInteger('is_confirm')->default(0)->comment = '员工是否第一次填写信息, 0:否 1:是';
        });
    }
}
