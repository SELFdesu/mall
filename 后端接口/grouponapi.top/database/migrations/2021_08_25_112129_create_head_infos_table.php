<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('head_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->string('name')->comment('申请人真实姓名');
            $table->string('tel')->comment('申请人联系方式');
            $table->string('id_number')->comment('申请人身份证号');
            $table->tinyInteger('identity')->comment('申请人身份 0便利店店长 1其他');
            $table->boolean('status')->default(1)->comment('状态 0禁用 1启用');
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
        Schema::dropIfExists('head_infos');
    }
}
