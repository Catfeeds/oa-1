<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 20)->comment = '资质的类型';
            $table->string('name')->comment = '资质名称';
            $table->text('content')->comment = '资质内容';
            $table->text('description')->comment = '说明';
            $table->integer('inv_remain')->comment = '库存总数';
            $table->string('company', 50)->comment = '所属公司';
            $table->boolean('is_annex')->comment = '是否上传附件';
            $table->boolean('is_show')->comment = '是否开启';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_inventory');
    }
}
