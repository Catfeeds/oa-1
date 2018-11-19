<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialApplyInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_apply_inventory', function (Blueprint $table) {
            $table->integer('apply_id')->comment = '物资申请id';
            $table->integer('inventory_id')->comment = '物资库存id';
            $table->boolean('part')->default(0)->comment = '该物资是否归还';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_apply_inventory');
    }
}
