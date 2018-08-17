<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesLeaveStepTable extends Migration
{
    /**
     * Run the migrations.
     *  职务审核步骤关联表
     * @return void
     */
    public function up()
    {
        Schema::create('roles_leave_step', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('step_id');

            $table->primary(['role_id', 'step_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_leave_step');
    }
}
