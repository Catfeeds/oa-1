<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryTable extends Migration
{
    /**
     * Run the migrations.
     * 员工入职登记表
     * @return void
     */
    public function up()
    {
        Schema::create('entry', function (Blueprint $table) {
            $table->increments('entry_id');
            ##管理员填写
            $table->string('name', 32)->comment = '员工姓名';
            $table->unsignedTinyInteger('sex')->default(0)->comment = '性别 0:男 1:女 2:未知';
            $table->string('mobile', 11)->comment = '联系电话(手机)';
            $table->string('email')->unique()->comment = '邮箱';
            $table->timestamp('entry_time')->comment = '预入职时间';
            $table->unsignedTinyInteger('nature_id')->default(0)->comment = '工作性质,具体看模型里面的配置';
            $table->unsignedTinyInteger('hire_id')->default(0)->comment = '招聘类型,具体看模型里面的配置';
            $table->unsignedInteger('firm_id')->comment = '所属公司ID';
            $table->unsignedInteger('dept_id')->comment = '部门ID';
            $table->unsignedInteger('job_id')->comment = '岗位类型ID';
            $table->string('job_name', 32)->comment = '岗位名称';
            $table->unsignedInteger('leader_id')->comment = '直属上级ID';
            $table->unsignedInteger('tutor_id')->comment = '导师ID';
            $table->unsignedInteger('friend_id')->comment = '基友ID';
            $table->string('place', 32)->nullable()->comment = '工作位置';
            $table->text('copy_user')->comment = '抄送人员ID，json格式存储';
            $table->unsignedTinyInteger('status')->default(0)->comment = '状态类型,具体看模型里面的配置';
            $table->unsignedInteger('creater_id')->comment = '创建者ID';
            $table->unsignedInteger('review_id')->nullable()->comment = '审核者ID';
            $table->rememberToken();
            $table->timestamp('send_time')->nullable()->comment = '发送时间';
            ##员工入职填写
            $table->string('card_id', 20)->nullable()->comment = '身份证号码';
            $table->string('card_address', 100)->nullable()->comment = '身份证地址';
            $table->string('ethnic', 32)->nullable()->comment = '民族';
            $table->string('birthplace', 20)->nullable()->comment = '籍贯';
            $table->string('political', 20)->nullable()->comment = '政治面貌';
            $table->string('census', 20)->nullable()->comment = '户籍类型';
            $table->integer('family_num')->nullable()->comment = '家庭成员人数';
            $table->unsignedTinyInteger('marital_status')->default(0)->comment = '婚姻状况 0:未婚 1:已婚';
            $table->unsignedTinyInteger('blood_type')->nullable()->comment = '血型';
            $table->integer('genus_id')->nullable()->comment = '属相类型ID,具体看模型里面的配置';
            $table->unsignedTinyInteger('constellation_id')->nullable()->comment = '星座类型ID,具体看模型里面的配置';
            $table->string('height', 3)->nullable()->comment = '身高';
            $table->string('weight', 3)->nullable()->comment = '体重';
            $table->string('qq', 20)->nullable()->comment = 'QQ号码';
            $table->string('live_address', 100)->nullable()->comment = '居住地址';
            $table->string('urgent_name', 20)->nullable()->comment = '紧急联系人姓名';
            $table->string('urgent_bind', 20)->nullable()->comment = '紧急联系人关系';
            $table->string('urgent_tel', 11)->nullable()->comment = '紧急联系人电话';
            $table->integer('education_id')->nullable()->comment = '学历';
            $table->integer('school_id')->nullable()->comment = '毕业学校';
            $table->timestamp('graduation_time')->nullable()->comment = '毕业时间';
            $table->string('specialty', 20)->nullable()->comment = '专业';
            $table->string('degree', 20)->nullable()->comment = '学位';
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
        Schema::dropIfExists('entry');
    }
}
