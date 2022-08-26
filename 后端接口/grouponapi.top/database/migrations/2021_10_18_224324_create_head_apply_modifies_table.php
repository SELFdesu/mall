<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadApplyModifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('head_apply_modifies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('head_id')->comment('团长id');
            $table->string('pic')->comment('店铺图片');
            $table->string('applicant')->comment('团长真实姓名');
            $table->string('tel')->comment('团长联系方式');
            $table->string('id_number')->comment('团长身份证号');
            $table->tinyInteger('identity')->comment('团长身份 0便利店店长 1其他');
            $table->string('store_name')->comment('团长店铺名称/自提点名称');
            $table->unsignedBigInteger('address')->comment('团长地址id');
            $table->string('store_address')->comment('团长店铺地址/自提点详细地址');
            $table->string('payment_method')->comment('打款方式');
            $table->string('payment_account')->comment('打款账号');
            $table->tinyInteger('status')->comment('处理状态 0不同意 1同意 2待处理');
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
        Schema::dropIfExists('head_apply_modifies');
    }
}
