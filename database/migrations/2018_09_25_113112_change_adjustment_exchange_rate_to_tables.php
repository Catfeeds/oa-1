<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAdjustmentExchangeRateToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmr_exchange_rate', function (Blueprint $table) {
            $table->decimal('exchange_rate', 12, 4)->default(0)->change()->comment = '汇率';
        });

        Schema::table('cmr_reconciliation', function (Blueprint $table) {
            $table->string('reconciliation_adjustment', 225)->default(0)->change()->comment = '对账调整';
            $table->string('reconciliation_rmb_adjustment', 225)->default(0)->change()->comment = '计提转化调整';
            $table->string('reconciliation_type', 225)->default(0)->change()->comment = '对账调整类型';
            $table->string('accrual_rmb_adjustment', 225)->default(0)->change()->comment = '计提转化调整';
            $table->string('accrual_adjustment', 225)->default(0)->change()->comment = '计提调整';
            $table->string('accrual_type', 225)->default(0)->change()->comment = '计提调整类型';
            $table->string('operation_adjustment', 225)->default(0)->change()->comment = '运营调整';
            $table->string('operation_rmb_adjustment', 225)->default(0)->change()->comment = '运营转化调整';
            $table->string('operation_type', 225)->default(0)->change()->comment = '运营调整类型';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cmr_exchange_rate', function (Blueprint $table) {
            $table->decimal('exchange_rate', 12, 2)->default(0)->change()->comment = '汇率';
        });

        Schema::table('cmr_reconciliation', function (Blueprint $table) {
            $table->decimal('reconciliation_adjustment', 12, 2)->default(0)->change()->comment = '对账调整';
            $table->decimal('reconciliation_rmb_adjustment', 12, 2)->default(0)->change()->comment = '计提转化调整';
            $table->unsignedInteger('reconciliation_type')->default(0)->change()->comment = '对账调整类型';
            $table->decimal('accrual_rmb_adjustment', 12, 2)->default(0)->change()->comment = '计提转化调整';
            $table->decimal('accrual_adjustment', 12, 2)->default(0)->change()->comment = '计提调整';
            $table->unsignedInteger('accrual_type')->default(0)->change()->comment = '计提调整类型';
            $table->decimal('operation_adjustment', 12, 2)->default(0)->change()->comment = '运营调整';
            $table->decimal('operation_rmb_adjustment', 12, 2)->default(0)->change()->comment = '运营转化调整';
            $table->unsignedInteger('operation_type')->default(0)->change()->comment = '运营调整类型';
        });
    }
}
