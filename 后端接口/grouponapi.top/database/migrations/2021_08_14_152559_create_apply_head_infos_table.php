<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplyHeadInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apply_head_infos', function (Blueprint $table) {
            $table->id();
            $table->string('pic')->default('defult.jpg')->comment('店铺图片');
            $table->string('applicant')->comment('申请人真实姓名');
            $table->string('tel')->comment('申请人联系方式');
            $table->string('id_number')->comment('申请人身份证号');
            $table->tinyInteger('identity')->comment('申请人身份 0便利店店长 1其他');
            $table->string('store_name')->comment('申请人店铺名称/自提点名称');
            $table->unsignedBigInteger('address')->comment('申请人地址id');
            $table->string('store_address')->comment('申请人店铺地址/自提点详细地址');
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
        Schema::dropIfExists('apply_head_infos');
    }
}
