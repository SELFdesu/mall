<?php
namespace App\Transformers;

use App\Models\ShoppingCart;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class ShoppingCartTransformer extends TransformerAbstract{
    //可include的方法
    protected $availableIncludes=['product'];

    public function transform(ShoppingCart $shoppingCart){
        return [
            'id'=>$shoppingCart->id,
            'product_id'=>$shoppingCart->product_id,
            'quantity'=>$shoppingCart->quantity,
        ];
    }

    public function includeProduct(ShoppingCart $shoppingCart){
        return $this->item($shoppingCart->product,new ProductPaginateTransformer());
    }


    
}
 