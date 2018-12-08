<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFieldEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entry', function (Blueprint $table) {
            $table->dropColumn('ethnic');
            $table->dropColumn('political');
            $table->string('role_id')->comment = '权限ID';
            $table->integer('ethnic_id')->nullable()->comment = '民族配置ID';
            $table->integer('political_id')->nullable()->comment = '政治面貌配置ID';
            $table->string('height', 10)->nullable()->change()->comment = '身高';
            $table->string('weight', 10)->nullable()->change()->comment = '体重';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entry', function (Blueprint $table) {
            $table->string('ethnic', 32)->nullable()->comment = '民族';
            $table->string('political', 20)->nullable()->comment = '政治面貌';
            $table->dropColumn('role_id');
            $table->dropColumn('ethnic_id');
            $table->dropColumn('political_id');
        });
    }
}
