<?php

namespace App\Http\Controllers\Admin;

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
    /**
     * 申请退货列表
     */
    public function salesReturnList(Request $request)
    {
        $request->validate([
            'status'=>'In:0,1,2,3,4,5',
            'cause'=>'In:1,2,3,4,5,6,7,8,9,10',
            'time_begin'=>'date',
            'time_end'=>'date',
        ]);
        
        $status=$request->input('status');
        $cause=$request->input('cause');
        $time_begin=$request->input('time_begin');
        $time_end=$request->input('time_end');

        if(isset($request->time_begin)||isset($request->time_end)){
            if(!isset($request->time_begin)||!isset($request->time_end)){
                return $this->response->errorBadRequest('请输入完整的时间区间');
            }
            if(strtotime($request->time_begin)>strtotime($request->time_end)){
                return $this->response->errorBadRequest('时间范围错误');
            }       
        }
        $salesReturnList=SalesReturnApply::when(isset($request->status),function($query) use($status) {
            $query->where('status',$status);
        })
        ->when(isset($request->cause),function($query) use($cause) {
            $query->where('cause',$cause);
        })
        ->when(isset($request->time_begin),function($query) use($time_begin,$time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->latest()
        ->paginate();
        return $this->response->paginator($salesReturnList,new SalesReturnTransformer());
    }

    /**
     * 申请退货详情
     */
    public function salesReturnInfo(SalesReturnApply $salesReturnApply)
    {
        return $this->response->item($salesReturnApply,new SalesReturnTransformer());
    }

    /**
     * 退货状态修改
     */
    public function status(Request $request, SalesReturnApply $salesReturnApply)
    {
        $request->validate([
            'status'=>'required|In:0,1,3,4'
        ]);
        $status=$request->input('status');

        if($status==0||$status==1){
            if($salesReturnApply->status!=5){
                return $this->response->errorBadRequest('退货申请状态错误！');
            }
        }
        if($status==3){
            if($salesReturnApply->status!=2){
                return $this->response->errorBadRequest('退货申请状态错误！');
            }
        }
        if($status==1&&$salesReturnApply->type==1){
            return $this->response->errorBadRequest('客户申请的为仅退款！');
        }
        if($status==4){
            if($salesReturnApply->type==2){
                if($salesReturnApply->status!=3){
                    return $this->response->errorBadRequest('退货申请状态错误！');
                }
            }

        }

        //事务处理
        DB::beginTransaction();
        try{
            if($status==1||$status==4){
                //将订单状态设为退款
                $salesReturnApply->order()->update([
                    'status'=>0
                ]);
            }

            //改变退款请求的状态
            $salesReturnApply->status=$status;
            $salesReturnApply->save();

            //退款操作
            if($status==4){
                $order=$salesReturnApply->order()->first();
                $payment_no=$order->payment_no;
                $order_amount=$order->amount;

                Pay::config(config('pay'));

                $back=Pay::alipay()->refund([
                    'trade_no' => $payment_no,
                    'refund_amount' => $order_amount,
                ]);
                if(!isset($back->code)){
                    throw new Exception('退款接口错误！');
                }
                if($back->code!=10000){
                    throw new Exception('退款失败');
                }
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

}
