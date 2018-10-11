<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMemoPunchRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('punch_record', function (Blueprint $table) {
            $table->string('memo', 32)->nullable()->after('status')->comment = '导入考勤信息备注';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('punch_record', function (Blueprint $table) {
            $table->dropColumn('memo');
        });
    }
}
