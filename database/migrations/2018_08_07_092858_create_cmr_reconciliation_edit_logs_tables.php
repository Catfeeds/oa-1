<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmrReconciliationEditLogsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmr_reconciliation_edit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment = '游戏ID';
            $table->timestamp('billing_cycle_start')->nullable()->comment = '结算开始周期';
            $table->timestamp('billing_cycle_end')->nullable()->comment = '结算结束周期';
            $table->string('client', 128)->nullable()->comment = '客户';
            $table->string('backstage_channel', 32)->nullable()->comment = '后台渠道';
            $table->decimal('adjustment', 12, 2)->default(0)->comment = '调整';
            $table->unsignedTinyInteger('type')->default(0)->comment = '调整类型';
            $table->string('remark', 225)->nullable()->comment = '调整备注';
            $table->string('user_name', 32)->nullable()->comment = '调整操作人';
            $table->timestamp('time')->nullable()->comment = '调整时间';

            $table->index(['product_id', 'user_name', 'time'], 'cmr_reconciliation_edit_logs_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cmr_reconciliation_edit_logs');
    }
}
