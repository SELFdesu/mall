<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('head_id')->comment('团长id');
            $table->decimal('salary',10,2)->comment('本月工资');
            $table->string('payment_method')->nullable()->comment('打款方式');
            $table->string('payment_account')->nullable()->comment('打款账号');
            $table->tinyInteger('status')->default(1)->comment('处理状态：0未处理 1已处理');
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
        Schema::dropIfExists('salaries');
    }
}
