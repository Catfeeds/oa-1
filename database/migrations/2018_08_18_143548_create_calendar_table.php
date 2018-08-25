<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarTable extends Migration
{
    /**
     * Run the migrations.
     * 日历表
     * @return void
     */
    public function up()
    {
        Schema::create('calendar', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('punch_rules_id')->defult(0)->comment = '排版规则ID';
            $table->unsignedInteger('year')->comment = '年';
            $table->unsignedInteger('month')->comment = '月';
            $table->unsignedInteger('day')->comment = '日';
            $table->unsignedInteger('week')->comment = '周';
            $table->text('memo')->nullable()->comment = '备注';

            $table->timestamps();

            $table->unique(['year', 'month', 'day', 'week'], 'unique_calendar_date');
            $table->index('punch_rules_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar');
    }
}
