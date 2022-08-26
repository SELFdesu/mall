<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadResignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('head_resigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('head_id');
            $table->unsignedBigInteger('user_id');
            $table->string('cause');
            $table->tinyInteger('type')->comment('0申请辞去团长 1请回恢复身份');
            $table->tinyInteger('status')->comment('0拒绝 1同意 2提交');
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
        Schema::dropIfExists('head_resigns');
    }
}
