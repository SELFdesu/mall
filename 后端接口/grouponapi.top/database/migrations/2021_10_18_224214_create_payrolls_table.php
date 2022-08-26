<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('head_id')->comment('团长id');
            $table->unsignedBigInteger('salary_id')->comment('对应工资表id');
            $table->decimal('salary',10,2)->comment('当月工资');
            $table->string('payment_method')->comment('打款方式');
            $table->string('payment_account')->comment('打款账号');
            $table->tinyInteger('status')->comment('处理状态：0未处理 1已处理');
            $table->string('date')->comment('日期');
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
        Schema::dropIfExists('payrolls');
    }
}
