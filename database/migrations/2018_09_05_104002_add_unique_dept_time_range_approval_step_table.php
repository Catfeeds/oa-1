<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueDeptTimeRangeApprovalStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approval_step', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('min_day');
            $table->dropColumn('max_day');
            $table->dropIndex('approval_step_name_index');

            $table->unsignedInteger('dept_id')->after('step_id')->comment = '部门ID';
            $table->unsignedInteger('time_range_id')->after('dept_id')->comment = '时间范围配置ID，具体可在模型中查看配置';

            $table->unique(['dept_id', 'time_range_id'], 'dept_step_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_step', function (Blueprint $table) {
            $table->string('name', 20)->after('step_id')->comment = '步骤名称';
            $table->decimal('min_day', 8,1)->after('name')->comment = '请假天数最小限制条件';
            $table->decimal('max_day', 8,1)->after('min_day')->comment = '请假天数最大限制条件';

        });
    }
}
