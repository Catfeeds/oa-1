<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersExtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_ext', function (Blueprint $table) {
            $table->increments('users_ext_id');
            $table->unsignedInteger('user_id')->comment = '用户唯一ID';
            $table->integer('school_id')->nullable()->comment = '毕业学校';
            $table->integer('education_id')->nullable()->comment = '学历';
            $table->timestamp('graduation_time')->nullable()->comment = '毕业时间';
            $table->integer('constellation_id')->nullable()->comment = '星座';
            $table->unsignedTinyInteger('blood_type')->nullable()->comment = '血型';
            $table->integer('age')->nullable()->comment = '年龄';
            $table->unsignedTinyInteger('sex')->default(0)->comment = '性别 0:男 1:女 2:未知';
            $table->timestamp('born')->nullable()->comment = '出生日期';
            $table->string('qq', 20)->nullable()->comment = 'QQ号码';
            $table->string('card_id', 20)->nullable()->comment = '身份证号码';
            $table->string('live_address', 100)->nullable()->comment = '居住地址';
            $table->string('card_address', 100)->nullable()->comment = '身份证地址';
            $table->string('birthplace', 20)->nullable()->comment = '籍贯';
            $table->unsignedTinyInteger('marital_status')->default(0)->comment = '婚姻状况 0:未婚 1:已婚';

            $table->integer('family_num')->nullable()->comment = '家庭成员';
            $table->string('census', 20)->nullable()->comment = '户籍类型';
            $table->unsignedTinyInteger('firm_call')->default(0)->comment = '是否公司挂靠 0:否 1:是';
            $table->string('urgent_name', 20)->nullable()->comment = '紧急联系人姓名';
            $table->string('urgent_tel', 11)->nullable()->comment = '紧急联系人电话';

            $table->timestamp('entry_time')->nullable()->comment = '入职时间';
            $table->timestamp('turn_time')->nullable()->comment = '转正时间';
            $table->integer('incumbent_num')->nullable()->comment = '在职年数';
            $table->timestamp('contract_st')->nullable()->comment = '合同开始时间';
            $table->timestamp('contract_et')->nullable()->comment = '合同到期时间';
            $table->integer('contract_years')->nullable()->comment = '合同年限';
            $table->integer('contract_num')->nullable()->comment = '合同签约次数';
            $table->string('salary_card', 20)->nullable()->comment = '工资卡';

            $table->unsignedTinyInteger('is_confirm')->default(0)->comment = '员工是否第一次填写信息, 0:否 1:是';

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_ext');
    }
}
