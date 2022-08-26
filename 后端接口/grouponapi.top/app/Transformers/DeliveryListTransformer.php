<?php
namespace App\Transformers;

use App\Models\PackageDeliver;
use League\Fractal\TransformerAbstract;

class DeliveryListTransformer extends TransformerAbstract{
    public function transform(PackageDeliver $packageDeliver){
        $time=get_deliver_time();
        $head=$packageDeliver->headInfo()->get()[0];
        $order=$packageDeliver->order()
            ->where('status',2)
            ->where('pay_time','<=',$time);
            
        $order_count=$order->count();

        return [
            'id'=>$packageDeliver->id,
            'pic'=>$packageDeliver->pic,
            'pic_url'=>oss_url($packageDeliver->pic,'delivery'),
            'head_name'=>$head->name,
            'store_name'=>$packageDeliver->store_name,
            'tel'=>$head->tel,
            'order_count'=>$order_count,
        ];
    }

    
}
 