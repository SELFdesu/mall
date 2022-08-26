<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplyHeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apply_heads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('申请人id');
            $table->unsignedBigInteger('info_id')->comment('申请详情信息id');
            $table->tinyInteger('apply_role_id')->comment('申请角色id 1网站管理员 2团长 3用户');
            $table->tinyInteger('status')->default(2)->comment('处理状态 0:申请不通过 1:申请通过 2:用户提交申请，未处理 ');
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
        Schema::dropIfExists('apply_heads');
    }
}
