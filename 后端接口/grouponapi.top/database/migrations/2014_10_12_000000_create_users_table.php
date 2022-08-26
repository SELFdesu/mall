<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->comment('用户名');
            $table->string('avatar')->default('default.jpg')->comment('用户头像');
            $table->string('tel')->unique()->nullable()->comment('手机号');
            $table->string('email')->unique()->nullable()->comment('电子邮箱');
            $table->string('password')->comment('密码');
            $table->boolean('sex')->comment('性别 0男 1女');
            $table->boolean('locked')->default(0)->comment('账号是否锁定 0正常 1锁定');
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
        Schema::dropIfExists('users');
    }
}
