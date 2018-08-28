<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRmbAdjustmentToCmrReconciliationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmr_reconciliation', function (Blueprint $table) {
            $table->decimal('operation_rmb_adjustment', 12, 2)->after('operation_adjustment')->comment = '运营转化调整';
            $table->decimal('accrual_rmb_adjustment', 12, 2)->after('accrual_adjustment')->comment = '计提转化调整';
            $table->decimal('reconciliation_rmb_adjustment', 12, 2)->after('reconciliation_adjustment')->comment = '对账转化调整';
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
            $table->dropColumn('operation_rmb_adjustment');
            $table->dropColumn('accrual_rmb_adjustment');
            $table->dropColumn('reconciliation_rmb_adjustment');
        });
    }
}
