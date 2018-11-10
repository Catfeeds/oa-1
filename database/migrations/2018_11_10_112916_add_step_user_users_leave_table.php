<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStepUserUsersLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_leave', function (Blueprint $table) {
            $table->text('step_user')->after('copy_user')->nullable()->comment = '审批步骤用户ID';
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
            $table->dropIndex('step_user');
        });
    }
}
