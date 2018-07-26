<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('username', 32)->unique()->comment = '账号';
            $table->string('alias', 32)->comment = '名称';
            $table->string('email')->unique()->comment = '邮箱';
            $table->string('mobile', 32)->nullable()->comment = '手机号';
            $table->boolean('is_mobile')->nullable()->comment = '是否验证手机号';
            $table->string('password', 60)->comment = '密码';
            $table->rememberToken();
            $table->tinyInteger('status')->default(1)->comment = '状态：0 不可用，1 可用';
            $table->integer('role_id')->index()->nullable()->comment = '权限角色ID';
            $table->integer('creater_id')->nullable()->index()->comment = '创建者ID';
            $table->string('desc')->nullable()->comment = '备注';
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
        Schema::dropIfExists('users');
    }
}
