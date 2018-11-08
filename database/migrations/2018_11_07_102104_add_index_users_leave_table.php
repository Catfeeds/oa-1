<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexUsersLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_leave', function (Blueprint $table) {
            $table->index('user_id', 'user_id_index');
            $table->index('start_time', 'start_time_index');
            $table->index('end_time', 'end_time_index');
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
            $table->dropIndex('user_id_index');
            $table->dropIndex('start_time_index');
            $table->dropIndex('end_time_index');
        });
    }
}
