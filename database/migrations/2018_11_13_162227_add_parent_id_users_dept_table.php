<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentIdUsersDeptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_dept', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->nullable()->comment = '默认空，部门父类ID';

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_dept', function (Blueprint $table) {
            $table->dropIndex('parent_id');
        });
    }
}
