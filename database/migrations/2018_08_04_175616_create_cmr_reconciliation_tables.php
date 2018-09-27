<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmrReconciliationTables extends Migration
{
    /**AA
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmr_reconciliation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment = '游戏ID';
            $table->timestamp('billing_cycle_start')->nullable()->comment = '结算开始周期';
            $table->string('income_type', 128)->nullable()->comment = '收入类型';
            $table->timestamp('billing_cycle_end')->nullable()->comment = '结算结束周期';
            $table->string('company', 128)->nullable()->comment = '公司';
            $table->string('client', 128)->nullable()->comment = '客户';
            $table->string('game_name', 32)->nullable()->comment = '游戏名';
            $table->string('online_name', 32)->nullable()->comment = '上线名称';
            $table->string('business_line', 32)->nullable()->comment = '业务线';
            $table->string('area', 32)->nullable()->comment = '地区';
            $table->string('reconciliation_currency', 32)->nullable()->comment = '对账币';
            $table->string('os', 16)->nullable()->comment = '系统';
            $table->string('divided_type', 16)->nullable()->comment = '分成类型';
            $table->string('backstage_channel', 32)->nullable()->comment = '后台渠道';
            $table->string('unified_channel', 32)->nullable()->comment = '统一渠道';
            $table->unsignedTinyInteger('review_type')->nullable()->comment = '审核状态 1未审核 2运营专员审核 3运营主管审核 4财务计提专员审核 5财务主管计提审核 6财务对账专员审核 7财务主管对账审核';

            $table->decimal('backstage_water_other', 12, 2)->default(0)->comment = '后台流水对账币';
            $table->decimal('backstage_water_rmb', 12, 2)->default(0)->comment = '后台流水人民币';

            $table->decimal('operation_adjustment', 12, 2)->default(0)->comment = '运营调整';
            $table->unsignedTinyInteger('operation_type')->default(0)->comment = '运营调整类型';
            $table->string('operation_remark', 225)->nullable()->comment = '运营调整备注';
            $table->string('operation_user_name', 32)->nullable()->comment = '运营调整操作人';
            $table->timestamp('operation_time')->nullable()->comment = '运营调整时间';
            $table->decimal('operation_water_other', 12, 2)->default(0)->comment = '运营流水对账币';
            $table->decimal('operation_water_rmb', 12, 2)->default(0)->comment = '运营流水人民币';

            $table->decimal('accrual_adjustment', 12, 2)->default(0)->comment = '计提调整';
            $table->unsignedTinyInteger('accrual_type')->default(0)->comment = '计提调整类型';
            $table->string('accrual_remark', 225)->nullable()->comment = '计提调整备注';
            $table->string('accrual_user_name', 32)->nullable()->comment = '计提调整操作人';
            $table->timestamp('accrual_time')->nullable()->comment = '计提调整时间';
            $table->decimal('accrual_water_other', 12, 2)->default(0)->comment = '计提流水对账币';
            $table->decimal('accrual_water_rmb', 12, 2)->default(0)->comment = '计提流水人民币';

            $table->decimal('reconciliation_adjustment', 12, 2)->default(0)->comment = '对账调整';
            $table->unsignedTinyInteger('reconciliation_type')->default(0)->comment = '对账调整类型';
            $table->string('reconciliation_remark', 225)->nullable()->comment = '对账调整备注';
            $table->string('reconciliation_user_name', 32)->nullable()->comment = '对账调整操作人';
            $table->timestamp('reconciliation_time')->nullable()->comment = '对账调整时间';
            $table->decimal('reconciliation_water_other', 12, 2)->default(0)->comment = '对账流水对账币';
            $table->decimal('reconciliation_water_rmb', 12, 2)->default(0)->comment = '对账流水人民币';

            $table->timestamps();

            $table->unique(['product_id', 'client', 'backstage_channel', 'billing_cycle_end'], 'cmr_reconciliation_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cmr_reconciliation');
    }
}
