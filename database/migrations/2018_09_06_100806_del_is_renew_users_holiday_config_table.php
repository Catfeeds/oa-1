<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DelIsRenewUsersHolidayConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_holiday_config', function (Blueprint $table) {
            $table->dropColumn('is_renew');
            $table->unsignedTinyInteger('is_full')->default(1)->after('num')->comment = '是否影响全勤 默认0 0:否 1:是';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_holiday_config', function (Blueprint $table) {
            $table->dropColumn('is_full');
            $table->unsignedTinyInteger('is_renew')->default(0)->after('is_boon')->comment = '福利假使用完是否可再提交申请假期 默认0 0:否 1:是';
        });
    }
}
