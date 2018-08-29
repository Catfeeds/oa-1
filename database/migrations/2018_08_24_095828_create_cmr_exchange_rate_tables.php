<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmrExchangeRateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmr_exchange_rate', function (Blueprint $table) {
            $table->increments('id');
            $table->string('billing_cycle', 32)->comment = '对账周期';
            $table->string('currency', 12)->comment = '货币';
            $table->decimal('exchange_rate', 12, 2)->default(0)->comment = '汇率';
            $table->unsignedTinyInteger('type')->default(1)->comment = '审核状态 1未编辑 2已编辑';

            $table->timestamps();
            $table->unique(['billing_cycle', 'currency'], 'unique_exchange_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cmr_exchange_rate');
    }
}
