<?php
namespace App\Transformers;

use App\Models\OrderDetail;
use League\Fractal\TransformerAbstract;

class OrderDetailTransformer extends TransformerAbstract{

    protected $availableIncludes=['order','product'];

    public function transform(OrderDetail $orderDetail){
        return [
            'id'=>$orderDetail->id,
            'order_id'=>$orderDetail->order_id,
            'product_id'=>$orderDetail->product_id,
            'unit_price'=>$orderDetail->unit_price,
            'quantity'=>$orderDetail->quantity,
            'total_amount'=>$orderDetail->total_amount,
        ];
    }

    public function includeOrder(OrderDetail $orderDetail){
        return $this->item($orderDetail->order,new OrderTransformer());
    }

    public function includeProduct(OrderDetail $orderDetail){
        return $this->item($orderDetail->product,new ProductTransformer());
    }


    
}
 