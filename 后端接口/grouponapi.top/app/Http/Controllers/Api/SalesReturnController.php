<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Models\SalesReturnApply;
use App\Transformers\SalesReturnTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yansongda\Pay\Pay;

class SalesReturnController extends BaseController
{
    public function salesReturnApply(Request $request,Order $order){
        $request->validate([
            'type'=>'required|In:1,2',
            'cause'=>'required|In:1,2,3,4,5,6,7,8,9,10',
            'describe'=>'max:255',
            'pics'=>'json'
        ]);
        $user=auth('api')->user();
        if($user->id!=$order->user_id){
            return $this->response->errorBadRequest('您无权对该订单申请退货！');
        }
        if(!in_array($order->status,[2,5,6])){
            return $this->response->errorBadRequest('当前状态无法申请退货！');
        }
        
        if(strtotime(time()-604800>strtotime($order->signfor_time))){
            return $this->response->errorBadRequest('仅支持签收七日内商品的退货申请！');
        }

        $salesReturnApply=$order->salesReturnApply();

        if($salesReturnApply->exists()){
            $salesReturnInfo=$salesReturnApply->first();
            if($salesReturnInfo->status==4){
                return $this->response->errorBadRequest('该订单已退货,请勿重复请求!');
            }
            if($salesReturnInfo->status!=0){
                return $this->response->errorBadRequest('该订单的退货申请正在处理中,请勿重复请求!');
            }else{
                $salesReturnInfo->update([
                    'type'=>$request->input('type'),
                    'cause'=>$request->input('cause'),
                    'describe'=>$request->input('describe',null),
                    'pics'=>$request->input('pics',null),
                    'status'=>5
                ]);
                return $this->response->created();
            } 
        }


        if(isset($request->pics)){
            $picsJson=json_decode($request->input('pics'));
            $checkPics=check_pics_json($picsJson);
            if($checkPics!==0){
                return $this->response->error($checkPics,422);
            }
        }
        //当订单状态为付款时直接退款
        if($order->status==2){
            if($request->input('type')!=1){
                return $this->response->errorBadRequest('您的商品未发货，请选择仅退款！');
            }
            if($request->input('cause')!=10){
                return $this->response->errorBadRequest('请选择"未发货商品申请退款"');
            }
            DB::beginTransaction();
            try{

                $order->status=0;
                $order->save();

                $order->salesReturnApply()->create([
                    'user_id'=>$order->user_id,
                    'type'=>1,
                    'cause'=>10,
                    'describe'=>$request->input('describe',null),
                    'pics'=>$request->input('pics',null),
                    'status'=>4
                ]);

                Pay::config(config('pay'));

                $back=Pay::alipay()->refund([
                    'trade_no' => $order->payment_no,
                    'refund_amount' => $order->amount,
                ]);
                if(!isset($back->code)){
                    throw new Exception('退款接口错误！');
                }
                if($back->code!=10000){
                    throw new Exception('退款失败');
                }

                DB::commit();

            } catch (Exception $e) {
                DB::rollback();
                if($e->getMessage()=='退款失败'){
                    return $e->getMessage();
                }
                return $this->response->errorInternal('数据库操作失败！请联系管理员。');
            }
            return $this->response->created();
        }
        //当订单已经发货时
        if($request->input('cause')==10){
            return $this->response->errorBadRequest('您的商品已经发货，不能选择"未发货商品申请退款"');
        }
        $order->salesReturnApply()->create([
            'user_id'=>$order->user_id,
            'type'=>$request->input('type'),
            'cause'=>$request->input('cause'),
            'describe'=>$request->input('describe',null),
            'pics'=>$request->input('pics',null),
            'status'=>5
        ]);
        return $this->response->created();
    }

    public function salesReturnSend(Request $request,Order $order){
        $request->validate([
            'express_type'=>'required',
            'express_no'=>'required',
        ]);
        $salesReturnApply=$order->salesReturnApply();

        if($salesReturnApply->exists()){
            $salesReturnInfo=$salesReturnApply->first();
            if($salesReturnInfo->type==1){
                return $this->response->errorBadRequest('您申请的退货类型为仅退款，无需退回货物!');
            }
            if($salesReturnInfo->status==4){
                return $this->response->errorBadRequest('该订单已退货,请勿重复请求!');
            }
            if($salesReturnInfo->status==0){
                return $this->response->errorBadRequest('您的退货申请未通过,如您对结果有异议请与负责人联系！');
            }
            if($salesReturnInfo->status!=1){
                return $this->response->errorBadRequest('该订单的退货申请正在处理中,请勿重复请求!');
            } 
        }else{
            return $this->response->errorBadRequest('您未对该订单发起退货请求！');
        }
        $order->salesReturnApply()->update([
            'express_type'=>$request->input('express_type'),
            'express_no'=>$request->input('express_no'),
            'status'=>2
        ]);
        return $this->response->created();
    }

    public function salesReturnInfo(Order $order)
    {
        $salesReturnApply=SalesReturnApply::where('order_id',$order->id)->first();
        return $this->response->item($salesReturnApply,new SalesReturnTransformer());
    }
}
