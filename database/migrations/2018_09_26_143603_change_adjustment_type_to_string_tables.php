<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAdjustmentTypeToStringTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cmr_reconciliation_edit_logs', function (Blueprint $table) {
            $table->string('adjustment', 225)->default(0)->change()->comment = '对账调整';
            $table->string('type', 225)->default(0)->change()->comment = '计提转化调整';
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
            $table->decimal('adjustment', 12, 2)->default(0)->change()->comment = '对账调整';
            $table->unsignedInteger('type')->default(0)->change()->comment = '对账调整类型';
        });
    }
}
