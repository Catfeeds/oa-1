<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldIsShowPunchRulesConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('punch_rules_config', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_show_start')->default(0)->comment = '是否显示 0:是 1:否';
            $table->unsignedTinyInteger('is_show_end')->default(0)->comment = '是否显示 0:是 1:否';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('punch_rules_config', function (Blueprint $table) {
            $table->dropColumn('is_show_start');
            $table->dropColumn('is_show_end');
        });
    }
}
