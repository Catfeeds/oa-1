<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersHolidayConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_holiday_config', function (Blueprint $table) {
            $table->increments('holiday_id');
            $table->string('holiday', 20)->comment = '假期类型名称';
            $table->text('memo')->nullable()->comment = '假期描述';
            $table->unsignedInteger('num')->defult(0)->comment = '假期天数';
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
        Schema::dropIfExists('users_holiday_config');
    }
}
