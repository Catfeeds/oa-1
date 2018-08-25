<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdToCrmTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmr_reconciliation_edit_logs', function (Blueprint $table) {
            $table->unsignedInteger('rec_id')->after('id')->comment = '对账审核ID';
        });

        Schema::table('cmr_reconciliation_proportion', function (Blueprint $table) {
            $table->unsignedInteger('rec_id')->after('id')->comment = '对账审核ID';
        });

        Schema::table('cmr_reconciliation', function (Blueprint $table) {
            $table->string('billing_cycle', 32)->after('id')->comment = '对账周期';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cmr_reconciliation_edit_logs', function (Blueprint $table) {
            $table->dropColumn('rec_id');
        });

        Schema::table('cmr_reconciliation_proportion', function (Blueprint $table) {
            $table->dropColumn('rec_id');
        });

        Schema::table('cmr_reconciliation', function (Blueprint $table) {
            $table->dropColumn('billing_cycle');
        });
    }
}
