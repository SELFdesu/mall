<?php
namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract{

    public function transform(Product $product){
        $pics_url=[];
        $pics=json_decode($product->pics)->urls ;
        foreach($pics as $val){
            array_push($pics_url,oss_url($val,'product'));
        }
        $category_name=$product->category()->first()->name;
        return [
            'id'=>$product->id,
            'title'=>$product->title,
            'picture'=>$product->picture,
            'picture_url'=>oss_url($product->picture,'product'),
            'pics'=>$pics,
            'pics_url'=>$pics_url,
            'category_id'=>$product->category_id,
            'category_name'=>$category_name,
            'price'=>$product->price,
            'stock'=>$product->stock,
            'commentnum'=>$product->commentnum,
            'sales'=>$product->sales,
            'description'=>$product->description,
            'attribute'=>json_decode($product->attribute),
            'is_on'=>$product->is_on,
            'is_recommend'=>$product->is_recommend,
        ];
    }


    
}
 