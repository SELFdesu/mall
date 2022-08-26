<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\PackageDeliver;
use App\Transformers\DeliveryListTransformer;
use App\Transformers\PackageDeliverTransformer;
use Illuminate\Http\Request;

class DeliveryController extends BaseController
{
    //自提点订单列表
    public function deliveryList(){
        $packageDeliver=PackageDeliver::paginate();
        return $this->response->paginator($packageDeliver,new DeliveryListTransformer());
    }

    //自提点订单详情
    public function deliveryInfo(PackageDeliver $packageDeliver){
        return $this->response->item($packageDeliver,new PackageDeliverTransformer());
    }


    //订单发货
    public function deliveryOrder(Request $request,PackageDeliver $packageDeliver){
        $request->validate([
            'express_type'=>'required|In:SF,YT,YD',
            'express_no'=>'required',

        ]);

        $time=get_deliver_time();

        $order=$packageDeliver->order()
        ->where('status',2)
        ->where('pay_time','<=',$time);
        
        if(!$order->count()){
            return $this->response->errorBadRequest('该自提点没有订单！');
        }



        $order->update([
                'express_type'=>$request->input('express_type'),
                'express_no'=>$request->input('express_no'),
                'status'=>3
            ]);
        return $this->response->created();
    }
    


}
