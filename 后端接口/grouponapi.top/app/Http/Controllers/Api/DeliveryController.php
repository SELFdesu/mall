<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\PackageDeliver;
use App\Models\UserDelivery;
use App\Transformers\BasicDeliveryListTransformer;
use Illuminate\Http\Request;


class DeliveryController extends BaseController
{
    //用户保存自提点列表
    public function UserDeliveryList(){
        $userDeliveryId=auth('api')->user()->userDeliver()->get();
        $deliveryIdArr=[];
        foreach($userDeliveryId as $val){
            array_push($deliveryIdArr,$val->delivery_id);
        }
        $userDeliveries=PackageDeliver::whereIn('id',$deliveryIdArr)->get();
        return $this->response->collection($userDeliveries,new BasicDeliveryListTransformer());
    }
    //新增保存自提点
    public function AddUserDelivery(Request $request){
        $request->validate([
            'delivery_id'=>'required|exists:package_delivers,id'
        ]);
        $userId=auth('api')->user();
        $userDeliveryId=$userId->userDeliver()->get();
        foreach($userDeliveryId as $val){
            if($val->delivery_id==$request->input('delivery_id')){
                return $this->response->errorBadRequest('您已保存该自提点!');
            }
        }
        UserDelivery::create([
            'user_id'=>$userId->id,
            'delivery_id'=>$request->input('delivery_id')
        ]);
        return $this->response->created();
    }
    //删除保存自提点
    public function DelUserDelivery(PackageDeliver $packageDeliver){
        $userId=auth('api')->user()->id;
        $deliverId=$packageDeliver->id;
        $userDelivery=UserDelivery::where('user_id',$userId)->where('delivery_id',$deliverId);
        if(!$userDelivery->exists()){
            return $this->response->errorBadRequest('您没有保存该自提点！');
        }
        $userDelivery->delete();
        return $this->response->noContent();
    }
}
