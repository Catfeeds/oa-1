<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResetTypeUsersHolidayConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_holiday_config', function (Blueprint $table) {
            $table->unsignedTinyInteger('reset_type')->default(0)->comment = '周期类型,默认0，0:不设置 1: 按入职时间 2:按自然周期 ,具体查看数据库模型配置';
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
            $table->dropColumn('reset_type');
        });
    }
}
