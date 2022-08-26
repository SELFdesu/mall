<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class PayController extends BaseController
{
    public function aliPay(Request $request,Order $order){
        $request->validate([
            'type'=>'required|In:web,wep,scan'
        ]);
        $payUserID=auth('api')->user()->id;
        if($payUserID!=$order->user_id){
            return $this->response->errorBadRequest('该订单不属于当前登录用户！');
        }
        if($order->status!=1){
            return $this->response->errorBadRequest('订单状态错误！');
        }
        $expire_time=date('Y-m-d H:i:s',time()+900);

        Pay::config(config('pay'));

        $type=$request->input('type');

        //订单信息
        $order_no=$order->id;
        $order_amount=$order->amount;

        if($type=='web'){
            return Pay::alipay()->web([
                'out_trade_no' => $order_no,
                'total_amount' => $order_amount,
                'subject' => '鲜橙团购订单支付',
                'time_expire'=>$expire_time
            ]);
        }elseif($type=='wep'){
            return Pay::alipay()->wap([
                'out_trade_no' => $order_no,
                'total_amount' => $order_amount,
                'subject' => '鲜橙团购订单支付',
                'quit_url' => 'http://47.100.229.1',
                'time_expire'=>$expire_time
             ]);
        }elseif($type=='scan'){
            $result = Pay::alipay()->scan([
                'out_trade_no' => $order_no,
                'total_amount' => $order_amount,
                'subject' => '鲜橙团购订单支付',
                'time_expire'=>$expire_time
            ]);
            return $result->qr_code; // 二维码 url
        }

    }


    // 支付宝的回调
    public function notifyAlipay(Request $request){
        $alipay = Pay::alipay(config('pay'));
    
        try{
            $data = $alipay->callback(); // 是的，验签就这么简单！

            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况
            if($data->trade_status=='TRADE_SUCCESS'||$data->trade_status=='TRADE_FINISHED'){
                DB::beginTransaction();
                //查询订单
                $order=Order::where('id',$data->out_trade_no);

                //订单不存在时
                if(!$order->exists()){

                    Pay::config(config('pay'));
                    Pay::alipay()->refund([
                        'trade_no' => $data->trade_no,
                        'refund_amount' => $data->total_amount,
                        'refund_reason' => '您支付的订单已不存在！'
                    ]);
                    Log::debug("Alipay notify",['订单不存在',$data]);

                }elseif($order->first()->amount!=$data->total_amount){
                    //支付金额错误时
                    Pay::config(config('pay'));
                    Pay::alipay()->refund([
                        'trade_no' => $data->trade_no,
                        'refund_amount' => $data->total_amount,
                        'refund_reason' => '您支付的金额有误！'
                    ]);
                    Log::debug("Alipay notify",['支付金额有误',$data]);

                }else{
                    $order->first()->update([
                        'status'=>2,
                        'pay_time'=>$data->gmt_payment,
                        'mode_of_payment'=>'alipay',
                        'payment_no'=>$data->trade_no
                    ]);
                    
                    $orderDetail=$order->first()->orderDetail()->get();
                    foreach($orderDetail as $val){
                        Product::where('id',$val->product_id)->increment('sales');
                    }
                    DB::commit();                    
                }

                
            }
            // Log::debug("Alipay notify",$data->all());
        } catch (Exception $e) {
            DB::rollback();
        }

        return $alipay->success();
    }



}
