<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('所属订单id');
            $table->unsignedBigInteger('product_id')->comment('所属商品id');
            $table->decimal('unit_price',10,2)->comment('成交时商品单价');
            $table->integer('quantity')->comment('商品数量');
            $table->decimal('total_amount',10,2)->comment('合计金额');

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
        Schema::dropIfExists('order_details');
    }
}
