<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIfSwitchFieldUsersLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_leave', function (Blueprint $table) {
            $table->mediumInteger('is_switch')->default(0)
                ->comment = '是否为转换的假期 默认为0 0:不是, 1:上午旷工转换 2:下午旷工 3: 一整天旷工 4:迟到转换 5:早退转换';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_leave', function (Blueprint $table) {
            $table->dropColumn('is_switch');
        });
    }
}
