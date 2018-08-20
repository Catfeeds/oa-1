<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunchRulesTable extends Migration
{
    /**
     * Run the migrations.
     *  上班/下班/假期 系统配置 时间规则表
     * @return void
     */
    public function up()
    {
        Schema::create('punch_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('punch_type_id')->comment = '规则类型 1:正常上班 2:休息日 3:节假日';
            $table->string('name', 32)->comment = '规则名称';
            $table->string('ready_time', 10)->nullable()->comment = '上班准备时间';
            $table->string('work_start_time', 10)->nullable()->comment = '上班时间';
            $table->string('work_end_time', 10)->nullable()->comment = '下班时间';

            $table->timestamps();

            $table->unique(['name']);
            $table->index('punch_type_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('punch_rules');
    }
}
