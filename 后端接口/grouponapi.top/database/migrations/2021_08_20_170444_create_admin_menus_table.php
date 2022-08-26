<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('分类名称');
            $table->unsignedBigInteger('pid')->default(0)->comment('父级id');
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
        Schema::dropIfExists('admin_menus');
    }
}
