<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateChangeUsersLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_leave', function (Blueprint $table) {
            $table->string('start_id', 10)->nullable()->change()->comment = '开始时间点';
            $table->string('end_id', 10)->nullable()->change()->comment = '结束时间点';
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
            $table->unsignedInteger('start_id')->nullable()->change()->comment = '开始时间ID';
            $table->unsignedInteger('end_id')->nullable()->change()->comment = '结束时间ID';
        });
    }
}
