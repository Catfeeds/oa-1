<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsBoolToUsersHolidayConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_holiday_config', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_boon')->default(0)->after('num')->comment = '是否员工福利假 默认0 0:否 1:是';
            $table->unsignedTinyInteger('is_renew')->default(0)->after('is_boon')->comment = '福利假使用完是否可再提交申请假期 默认0 0:否 1:是';
            $table->unsignedTinyInteger('is_annex')->default(0)->after('is_renew')->comment = '是否上传附件 默认0 0:否 1:是';
            $table->unsignedTinyInteger('condition_id')->nullable()->after('is_annex')->comment = '福利假重置条件ID';
            $table->unsignedTinyInteger('restrict_sex')->default(2)->after('condition_id')->comment = '是否现在男女,默认2，不限 0:男 1:女';
            $table->unsignedTinyInteger('punch_type')->default(0)->after('restrict_sex')->comment = '补打卡类型 默认0 0:不设置 1:上班 2:下班';

            $table->unique(['apply_type_id', 'holiday'], 'holiday_config');

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
            $table->dropColumn('is_boon');
            $table->dropColumn('is_renew');
            $table->dropColumn('is_annex');
            $table->dropColumn('condition_id');
            $table->dropColumn('restrict_sex');
            $table->dropUnique('holiday_config');
        });
    }
}
