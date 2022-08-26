<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('所属用户id');
            
            $table->unsignedBigInteger('package_deliver_id')->comment('自提点id');
            $table->string('express_type')->nullable()->comment('快递类型 SF YT YD');
            $table->string('express_no')->nullable()->comment('快递单号');

            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->string('mode_of_payment')->nullable()->comment('付款方式 wx,alipay');
            $table->string('payment_no')->nullable()->comment('支付单号');

            $table->timestamp('signfor_time')->nullable()->comment('签收时间');

            $table->tinyInteger('status')->default(1)->comment('订单状态 0已退货 1下单 2支付 3发货 4到达自提点 5用户收货 6用户评价 99订单失效');

            $table->decimal('amount',10,2)->comment('订单总金额');
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
        Schema::dropIfExists('orders');
    }
}
