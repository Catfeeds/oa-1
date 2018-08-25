<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmrReconciliationDifferenceTypeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmr_reconciliation_difference_type', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment = '游戏ID';
            $table->string('type_name', 32)->comment = '差异类型名';

            $table->timestamps();

            $table->unique(['product_id', 'type_name'], 'cmr_reconciliation_difference_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cmr_reconciliation_difference_type');
    }
}
