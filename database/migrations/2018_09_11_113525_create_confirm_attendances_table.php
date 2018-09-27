<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfirmAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirm_attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment = "用户唯一ID";
            $table->unsignedInteger('year')->comment = "年";
            $table->unsignedInteger('month')->comment = "月";
            $table->unsignedTinyInteger('confirm')->default(0)->comment = "确认状态 0:未发送 1:已发布未确认 2:已确认";

            $table->index('user_id');
            $table->unique(['user_id', 'year', 'month'], 'unique_date');
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
        Schema::dropIfExists('confirm_attendances');
    }
}
