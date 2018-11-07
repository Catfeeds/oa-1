<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExceedDayUsersLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_leave', function (Blueprint $table) {
            $table->double('exceed_day', 8, 1)->after('number_day')->nullable()->comment = '超出计薪天数';
            $table->unsignedInteger('exceed_holiday_id')->after('exceed_day')->nullable()->comment = '超出计薪天数转换ID';
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
            $table->dropColumn('exceed_day');
            $table->dropColumn('exceed_holiday_id');
        });
    }
}
