<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeleteProductIdTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmr_reconciliation_difference_type', function (Blueprint $table) {
            $table->dropColumn('product_id');
            $table->dropUnique('cmr_reconciliation_difference_type_unique');
            $table->unique(['type_name'], 'cmr_reconciliation_difference_type_unique_tables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cmr_reconciliation_difference_type', function (Blueprint $table) {
            $table->unsignedInteger('product_id')->comment = '游戏ID';
            $table->unique(['product_id', 'type_name'], 'cmr_reconciliation_difference_type_unique');
            $table->dropUnique('cmr_reconciliation_difference_type_unique_tables');
        });
    }
}
