<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesReturnAppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_return_applies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->unsignedBigInteger('order_id')->comment('订单id');
            $table->tinyInteger('type')->comment('申请退货类型 1仅退款 2退货退款');
            $table->tinyInteger('cause')->comment('退货原因 1做工问题 2质量问题 3配件问题 4商品破损 5未按时间发货 6发错货 7少件漏发 8快件丢失 9其他，10未发货商品申请退款');
            $table->string('describe')->nullable()->comment('退货原因描述');
            $table->json('pics')->nullable()->comment('图片信息');
            $table->tinyInteger('status')->comment('状态 0拒绝退货 1同意退货 2用户寄出 3平台签收 4退货完成 5未处理');
            $table->string('express_type')->nullable()->comment('快递类型 SF YT YD');
            $table->string('express_no')->nullable()->comment('快递单号');
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
        Schema::dropIfExists('sales_return_applies');
    }
}
