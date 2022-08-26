<?php
namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductPaginateTransformer extends TransformerAbstract{

    public function transform(Product $product){
        $category_name=$product->category()->first()->name;
        return [
            'id'=>$product->id,
            'title'=>$product->title,
            'picture'=>$product->picture,
            'picture_url'=>oss_url($product->picture,'product'),
            'category_id'=>$product->category_id,
            'category_name'=>$category_name,
            'price'=>$product->price,
            'commentnum'=>$product->commentnum,
            'sales'=>$product->sales,
            'is_on'=>$product->is_on,
            'is_recommend'=>$product->is_recommend,
            'stock'=>$product->stock,
        ];
    }


    
}
 