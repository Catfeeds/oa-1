<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCopyUserFieldUsersEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_entry', function (Blueprint $table) {
            $table->text('copy_user')->nullable()->change()->comment = '抄送人员ID，json格式存储';
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
