<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageDeliversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_delivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('head_id')->comment('团长id');
            $table->string('pic')->default('defult.jpg')->comment('店铺图片');
            $table->string('store_name')->comment('申请人店铺名称/自提点名称');
            $table->unsignedBigInteger('address_id')->comment('地址id 省、市、县');
            $table->string('address_info')->comment('详细地址');
            $table->boolean('status')->default(1)->comment('状态');
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
        Schema::dropIfExists('package_delivers');
    }
}
