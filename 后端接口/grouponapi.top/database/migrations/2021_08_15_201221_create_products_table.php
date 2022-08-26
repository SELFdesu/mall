<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('商品标题');
            $table->string('picture')->comment('商品展示图片');
            $table->json('pics')->comment('小图集');
            $table->unsignedBigInteger('category_id')->comment('分类id ');
            $table->decimal('price',10,2)->comment('价格');
            $table->unsignedInteger('stock')->comment('库存');
            $table->unsignedInteger('commentnum')->default(0)->comment('评论数量');
            $table->unsignedInteger('sales')->default(0)->comment('销售量');
            $table->text('description')->comment('描述');
            $table->json('attribute')->comment('规格属性');
            $table->boolean('is_on')->default(false)->comment('是否上架 0不上架 1上架');
            $table->boolean('is_recommend')->default(false)->comment('是否推荐 0不推荐 1推荐');
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
        Schema::dropIfExists('products');
    }
}
