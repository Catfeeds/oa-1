<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndDateFieldBulletinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulletin', function (Blueprint $table) {
            $table->dropColumn('valid_time');

            $table->date('start_date')->after('content')->comment = '公告栏开始显示时间';
            $table->date('end_date')->after('start_date')->comment = '公告栏结束显示时间';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulletin', function (Blueprint $table) {
            $table->unsignedMediumInteger('valid_time')->default(1)->comment = '有效日期多少天';
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
}