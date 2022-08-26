<?php
namespace App\Transformers;

use App\Models\Order;
use App\Models\PackageDeliver;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract{

    protected $availableIncludes=['user','orderDetail'];

    public function transform(Order $order){

        $packageDeliverName=PackageDeliver::select('store_name')
            ->find($order->package_deliver_id)
            ->store_name;

        // 0已退货 1下单 2支付 3发货 4到达自提点 5用户收货 6用户评价 99订单失效
        $status=$order->status;
        if($status==0){
            $statusName='已退货';
        }elseif($status==1){
            $statusName='下单';
        }elseif($status==2){
            $statusName='支付';
        }elseif($status==3){
            $statusName='发货';
        }elseif($status==4){
            $statusName='到达自提点';
        }elseif($status==5){
            $statusName='用户收货';
        }elseif($status==6){
            $statusName='用户评价';
        }elseif($status==99){
            $statusName='订单已失效';
        }else{
            $statusName='error';
        }

        if($order->status==1){
            $time_remaining=time()-strtotime($order->created_at);
            if($time_remaining<=900){
                $expiry_date=(strtotime($order->created_at)+900)-time();
            }else{
                $expiry_date='overdue';
            }
        }else{
            $expiry_date='permanent';
        }
        
        return [
            'id'=>$order->id,
            'user_id'=>$order->user_id,
            'package_deliver_id'=>$order->package_deliver_id,
            'package_deliver_name'=>$packageDeliverName,
            'express_type'=>$order->express_type,
            'express_no'=>$order->express_no,
            'pay_time'=>$order->pay_time,
            'mode_of_payment'=>$order->mode_of_payment,
            'payment_no'=>$order->payment_no,
            'signfor_time'=>$order->signfor_time,
            'status'=>$order->status,
            'status_name'=>$statusName,
            'amount'=>$order->amount,
            'created_at'=>$order->created_at,
            'expiry_date'=>$expiry_date
        ];
    }

    public function includeUser(Order $order){
        return $this->item($order->user,new UserTransformer());
    }

    public function includeOrderDetail(Order $order){
        return $this->collection($order->orderDetail,new OrderDetailTransformer());
    }


    
}
 