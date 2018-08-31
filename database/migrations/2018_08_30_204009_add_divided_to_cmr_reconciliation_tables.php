<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDividedToCmrReconciliationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmr_reconciliation', function (Blueprint $table) {
            $table->decimal('operation_divide_other', 12, 2)->default(0)->after('operation_water_rmb')->comment = '运营对账币分成';
            $table->decimal('operation_divide_rmb', 12, 2)->default(0)->after('operation_divide_other')->comment = '运营RMB分成';

            $table->decimal('accrual_divide_other', 12, 2)->default(0)->after('accrual_water_rmb')->comment = '计提对账币分成';
            $table->decimal('accrual_divide_rmb', 12, 2)->default(0)->after('accrual_divide_other')->comment = '计提RMB分成';
            $table->dropUnique('cmr_reconciliation_unique');

            $table->index(['billing_cycle', 'product_id', 'review_type'], 'cmr_reconciliation_index');
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
            $table->dropColumn('operation_divide_other');
            $table->dropColumn('operation_divide_rmb');

            $table->dropColumn('accrual_divide_other');
            $table->dropColumn('accrual_divide_rmb');

            $table->unique(['product_id', 'client', 'backstage_channel', 'billing_cycle_end'], 'cmr_reconciliation_unique');
            $table->dropIndex('cmr_reconciliation_index');
        });
    }
}
