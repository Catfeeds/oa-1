<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmrReconciliationPrincipalTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmr_reconciliation_principal', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment = '游戏ID';
            $table->unsignedInteger('job_id')->comment = '职位ID';
            $table->unsignedInteger('principal_id')->default(0)->comment = '负责人ID';

            $table->timestamps();

            $table->unique(['product_id', 'job_id'], 'cmr_reconciliation_principal_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cmr_reconciliation_principal');
    }
}
