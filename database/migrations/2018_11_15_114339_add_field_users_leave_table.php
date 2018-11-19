<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldUsersLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_leave', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->nullable()->comment = '默认空，申请单父类ID';
            $table->unsignedTinyInteger('is_stat')->default(0)->comment = '是否统计 默认0 0:是 1:否';
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
            $table->dropIndex('parent_id');
            $table->dropIndex('is_stat');
        });
    }
}
