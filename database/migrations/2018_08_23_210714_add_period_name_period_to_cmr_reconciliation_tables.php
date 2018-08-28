<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPeriodNamePeriodToCmrReconciliationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmr_reconciliation', function (Blueprint $table) {
            $table->string('period_name', 32)->after('unified_channel')->comment = '信期类型名字';
            $table->unsignedInteger('period')->after('period_name')->comment = '信期';
            $table->unsignedTinyInteger('billing_type')->default(1)->after('period')->comment = '开票状态 1:未开票 2:已开票';
            $table->string('billing_num',64)->nullable()->after('billing_type')->comment = '开票号';
            $table->timestamp('billing_time')->nullable()->after('billing_num')->comment = '开票时间';
            $table->string('billing_user', 32)->nullable()->after('billing_time')->comment = '开票人';
            $table->unsignedTinyInteger('payback_type')->default(1)->after('billing_user')->comment = '回款状态 1:未回款 2:已回款';
            $table->timestamp('payback_time')->nullable()->after('payback_type')->comment = '回款时间';
            $table->string('payback_user', 32)->nullable()->after('payback_time')->comment = '回款确认人';

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cmr_reconciliation', function (Blueprint $table) {
            $table->dropColumn('period_name');
            $table->dropColumn('period');
            $table->dropColumn('billing_type');
            $table->dropColumn('billing_num');
            $table->dropColumn('billing_time');
            $table->dropColumn('billing_user');
            $table->dropColumn('payback_type');
            $table->dropColumn('payback_time');
            $table->dropColumn('payback_user');
        });
    }
}