<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('分类名称');
            $table->string('pic')->default('default.jpg')->comment('分类图片');
            $table->unsignedBigInteger('pid')->comment('父级');
            $table->tinyInteger('status')->default(1)->comment('状态 0禁用 1启用');
            $table->tinyInteger('level')->default(1)->comment('分类级别1 2');
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
        Schema::dropIfExists('categories');
    }
}
