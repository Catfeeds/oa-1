<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormulaUsersHolidayConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_holiday_config', function (Blueprint $table) {
            $table->string('work_reset_formula', 32)->after('work_relief_type')->nullable()->comment = '加班调休周期重置公式，{月,日,时,分,秒}';
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
            $table->dropColumn('work_reset_formula');
        });
    }
}
