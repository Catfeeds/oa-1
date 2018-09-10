<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNumberDayUsersLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_leave', function (Blueprint $table) {
            $table->decimal('number_day', 8, 1)->after('end_id')->comment = '请假天数';
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
            $table->dropColumn('number_day');
        });
    }
}
