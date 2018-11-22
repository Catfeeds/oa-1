<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFieldUsersHolidayConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_holiday_config', function (Blueprint $table) {
            $table->dropColumn('num');
            $table->dropColumn('is_boon');
            $table->dropColumn('condition_id');
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
            $table->unsignedInteger('num')->defult(0)->comment = '假期天数';
            $table->unsignedTinyInteger('is_boon')->default(0)->after('num')->comment = '是否员工福利假 默认0 0:否 1:是';
            $table->unsignedTinyInteger('condition_id')->nullable()->after('is_annex')->comment = '福利假重置条件ID';
        });
    }
}
