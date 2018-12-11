<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEntryTimeFieldUsersEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_entry', function (Blueprint $table) {
            $table->date('entry_time')->default('0000-00-00')->change()->comment = '预入职时间';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_entry', function (Blueprint $table) {
            //
        });
    }
}
