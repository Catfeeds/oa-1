<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTypeToCmrReconciliationProportionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmr_reconciliation_proportion', function (Blueprint $table) {
            $table->dropColumn('review_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cmr_reconciliation_proportion', function (Blueprint $table) {
            $table->unsignedInteger('review_type')->nullable()->comment = '审核状态 1未审核 2已审核';
        });
    }
}
