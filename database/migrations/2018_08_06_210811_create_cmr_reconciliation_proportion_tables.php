<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmrReconciliationProportionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmr_reconciliation_proportion', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment = '游戏ID';
            $table->timestamp('billing_cycle')->nullable()->comment = '结算周期';
            $table->string('client', 128)->nullable()->comment = '客户';
            $table->string('backstage_channel', 32)->nullable()->comment = '后台渠道';
            $table->double('channel_rate', 3, 2)->default(0)->comment = '渠道费率';
            $table->double('first_division', 5, 2)->default(0)->comment = '一级分成';
            $table->string('first_division_remark', 225)->nullable()->comment = '一级分成备注';
            $table->double('second_division', 5, 2)->default(0)->comment = '二级分成';
            $table->string('second_division_remark', 225)->nullable()->comment = '二级分成备注';
            $table->decimal('second_division_condition', 12, 2)->default(0)->comment = '二级分成条件';
            $table->string('user_name', 32)->nullable()->comment = '操作人';
            $table->unsignedBigInteger('review_type')->nullable()->comment = '审核状态 1未审核 2已审核';

            $table->timestamps();

            $table->unique(['product_id', 'client', 'backstage_channel', 'billing_cycle'], 'cmr_reconciliation_proportion_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cmr_reconciliation_proportion');
    }
}
