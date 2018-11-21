<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFieldPunchRulesConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('punch_rules_config', function (Blueprint $table) {
            $table->decimal('ded_num', 8, 1)->default(0)->change()->comment = '扣分单位';
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
            $table->unsignedInteger('ded_num')->nullable()->change()->comment = '扣分单位';
        });
    }
}
