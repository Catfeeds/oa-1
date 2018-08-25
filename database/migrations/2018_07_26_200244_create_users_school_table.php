<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSchoolTable extends Migration
{
    /**
     * Run the migrations.
     *  员工 系统配置 学校配置表
     * @return void
     */
    public function up()
    {
        Schema::create('users_school', function (Blueprint $table) {
            $table->increments('school_id');
            $table->string('school', 50)->comment = '毕业学校';
            $table->timestamps();

            $table->unique(['school']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_school');
    }
}
