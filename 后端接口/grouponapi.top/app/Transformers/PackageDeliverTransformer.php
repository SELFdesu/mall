<?php
namespace App\Transformers;

use App\Models\PackageDeliver;
use League\Fractal\TransformerAbstract;
use Cblink\Region\Region;

class PackageDeliverTransformer extends TransformerAbstract{

    protected $availableIncludes=['order','headInfo'];

    public function transform(PackageDeliver $packageDeliver){
        $time=get_deliver_time();
        $order=$packageDeliver->order()
            ->where('status',2)
            ->where('pay_time','<=',$time)
            ->get();
            
        $orderDetailArr=[];
        foreach($order as $val){
            array_push($orderDetailArr,$val->orderDetail()
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->select('product_id','quantity','products.title','products.picture','order_details.total_amount')
                ->get()
            );
        }
        $countProduct=[];
        foreach($orderDetailArr as $val){
            foreach($val as $innerVal){
                if(!isset($countProduct[$innerVal->product_id])){
                    $data=[
                        "product_id"=>$innerVal->product_id,
                        "title"=>$innerVal->title,
                        "picture"=>$innerVal->picture,
                        "picture_url"=>oss_url($innerVal->picture,'product'),
                        "quantity"=>$innerVal->quantity,
                        "total_amount"=>$innerVal->total_amount
                    ];
                    $countProduct[$innerVal->product_id]=$data;

                }else{
                    $countProduct[$innerVal->product_id]["quantity"]+=$innerVal->quantity;
                    $countProduct[$innerVal->product_id]["total_amount"]+=$innerVal->total_amount;
                }
            }
        }
        $dispose_count=[];
        foreach($countProduct as $val){
            array_push($dispose_count,$val);
        }

        $address_id=$packageDeliver->address_id;


        return [
            'id'=>$packageDeliver->id,
            'head_id'=>$packageDeliver->head_id,
            'pic'=>$packageDeliver->pic,
            'pic_url'=>oss_url($packageDeliver->pic,'delivery'),
            'store_name'=>$packageDeliver->store_name,
            'address_id'=>$packageDeliver->address_id,
            'address_name'=>get_region_name($address_id),
            'address_info'=>$packageDeliver->address_info,
            'status'=>$packageDeliver->status,
            'products'=>$dispose_count
        ];
    }

    public function includeOrder(PackageDeliver $packageDeliver){
        return $this->collection($packageDeliver->order,new OrderTransformer());
    }

    public function includeHeadInfo(PackageDeliver $packageDeliver){
        return $this->item($packageDeliver->headInfo,new HeadInfoTransformer());
    }

    
}
 