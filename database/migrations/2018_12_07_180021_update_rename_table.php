<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRenameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('users_dept', 'sys_users_dept');
        Schema::rename('users_ethnic', 'sys_users_ethnic');
        Schema::rename('users_holiday_config', 'sys_attendance_holiday_config');
        Schema::rename('users_job', 'sys_users_job');
        Schema::rename('users_school', 'sys_users_school');
        Schema::rename('firm', 'sys_users_firm');
        Schema::rename('calendar', 'sys_attendance_calendar');
        Schema::rename('bulletin', 'sys_bulletin');
        Schema::rename('entry', 'users_entry');
        Schema::rename('appeal', 'attendance_appeal');
        Schema::rename('punch_record', 'attendance_punch_record');
        Schema::rename('users_leave', 'attendance_users_leave');
        Schema::rename('punch_rules', 'sys_attendance_punch_rules');
        Schema::rename('punch_rules_config', 'sys_attendance_punch_rules_config');
        Schema::rename('review_step_flow', 'sys_attendance_review_step_flow');
        Schema::rename('review_step_flow_config', 'sys_attendance_review_step_flow_config');
        Schema::rename('material_inventory', 'sys_material_inventory');
        Schema::rename('confirm_attendances', 'attendance_confirm');

        ##删除废弃表
        Schema::drop('approval_step');
        Schema::drop('roles_leave_step');
        Schema::drop('users_holiday');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
