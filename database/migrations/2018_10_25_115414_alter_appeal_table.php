<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAppealTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appeal', function (Blueprint $table) {
            $table->dropColumn('apply_type_id');
            $table->dropColumn('leave_id');
            $table->dropColumn('daily_id');

            $table->unsignedInteger('appeal_id')->after('user_id')->comment = "申诉id";
            $table->unique(['appeal_type', 'user_id', 'appeal_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appeal', function (Blueprint $table) {
            $table->dropUnique(['appeal_type', 'user_id', 'appeal_id']);

            $table->unsignedInteger('apply_type_id')->nullable()->comment = "若请假申诉,申诉的请假类型";
            $table->unsignedInteger('leave_id')->nullable()->comment = "若请假申诉,申诉的假期id";
            $table->unsignedInteger('daily_id')->nullable()->comment = "若明细申诉,申诉的每日明细id";
            $table->dropColumn('appeal_id');
        });
    }
}
