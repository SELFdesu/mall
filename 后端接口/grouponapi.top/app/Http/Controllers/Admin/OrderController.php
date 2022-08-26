<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Transformers\OrderDetailTransformer;
use App\Transformers\OrderTransformer;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    /**
     * 订单列表(支付方式、快递类型、订单状态、时间区间)
     */
    public function index(Request $request)
    {
        $request->validate([
            'mode_of_payment'=>'In:wx,alipay',
            'express_type'=>'In:SF,YT,YD',
            'status'=>'In:0,1,2,3,4,5,6,99',
            'time_begin'=>'date',
            'time_end'=>'date',
        ]);


        if(isset($request->time_begin)||isset($request->time_end)){
            if(!isset($request->time_begin)||!isset($request->time_end)){
                return $this->response->errorBadRequest('请输入完整的时间区间');
            }
            if(strtotime($request->time_begin)>strtotime($request->time_end)){
                return $this->response->errorBadRequest('时间范围错误');
            }       
        }

        //订单状态 0已退货 1下单 2支付 3发货 4到达自提点 5用户收货 6用户评价 99订单失效
        $status=$request->input('status');

        $mode_of_payment=$request->input('mode_of_payment');

        $express_type=$request->input('express_type');

        $time_begin=$request->input('time_begin');
        $time_end=$request->input('time_end');

        $orders=Order::when(isset($request->status),function($query) use($status){
            $query->where('status',$status);
        })
        ->when(isset($request->time_begin),function($query) use($time_begin,$time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->when(isset($request->mode_of_payment),function($query) use($mode_of_payment){
            $query->where('mode_of_payment',$mode_of_payment);
        })
        ->when(isset($request->express_type),function($query) use($express_type){
            $query->where('express_type',$express_type);
        })
        ->latest()
        ->paginate(15);
        return $this->response->paginator($orders,new OrderTransformer());
    }

    /**
     * 订单搜索(订单号、快递单号、支付单号)
     */
    public function serch(Request $request){
        $request->validate([
            'type'=>'required|In:id,express_no,payment_no',
            'number'=>'required'
        ]);
        $type=$request->input('type');
        $number=$request->input('number');
        $order=Order::when($type=='id',function($query)use($number){
            $query->where('id',$number);
        })
        ->when($type=='express_no',function($query)use($number){
            $query->where('express_no',$number);
        })
        ->when($type=='payment_no',function($query)use($number){
            $query->where('payment_no',$number);
        })
        ->latest()
        ->paginate(15);
        return $this->response->paginator($order,new OrderTransformer());
    }

    /**
     * 订单详情
     */
    public function show(Order $order)
    {   
        $orderDetail=$order->orderDetail;
        return $this->response->collection($orderDetail,new OrderDetailTransformer());
    }



}
