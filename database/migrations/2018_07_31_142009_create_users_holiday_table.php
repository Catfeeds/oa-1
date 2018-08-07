<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersHolidayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_holiday', function (Blueprint $table) {
            $table->integer('user_id')->comment = '员工ID';
            $table->integer('holiday_id')->comment = '假期类型ID';
            $table->integer('num')->comment = '假期天数';

            $table->unique(['user_id', 'holiday_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_holiday');
    }
}
