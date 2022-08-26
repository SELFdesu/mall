<?php
namespace App\Transformers;

use App\Models\SalesReturnApply;
use League\Fractal\TransformerAbstract;

class SalesReturnTransformer extends TransformerAbstract{

    protected $availableIncludes=['user','order','orderDetail'];

    public function transform(SalesReturnApply $salesReturnApply){
        //退货原因 1做工问题 2质量问题 3配件问题 4商品破损 5未按时间发货 6发错货 7少件漏发
        if($salesReturnApply->cause==1){
            $cause='做工问题';
        }elseif($salesReturnApply->cause==2){
            $cause='质量问题';
        }elseif($salesReturnApply->cause==3){
            $cause='配件问题';
        }elseif($salesReturnApply->cause==4){
            $cause='商品破损';
        }elseif($salesReturnApply->cause==5){
            $cause='未按时间发货';
        }elseif($salesReturnApply->cause==6){
            $cause='发错货';
        }elseif($salesReturnApply->cause==7){
            $cause='少件漏发';
        }elseif($salesReturnApply->cause==8){
            $cause='快件丢失';
        }elseif($salesReturnApply->cause==9){
            $cause='其他';
        }elseif($salesReturnApply->cause==10){
            $cause='未发货商品申请退款';
        }

        //状态 0拒绝退货 1同意退货 2用户寄出 3平台签收 4退货完成 5未处理
        if($salesReturnApply->status==0){
            $status='拒绝退货';
        }elseif($salesReturnApply->status==1){
            $status='同意退货';
        }elseif($salesReturnApply->status==2){
            $status='用户寄出';
        }elseif($salesReturnApply->status==3){
            $status='平台签收';
        }elseif($salesReturnApply->status==4){
            $status='退货完成';
        }elseif($salesReturnApply->status==5){
            $status='未处理';
        }
        
        $pics_url=[];
        if($salesReturnApply->pics){
            $pics=json_decode($salesReturnApply->pics)->urls ;
            foreach($pics as $val){
                array_push($pics_url,oss_url($val,'sales_return'));
            }   
        }
        
        if($salesReturnApply->type==1){
            $type_name='仅退款';
        }else{
            $type_name='退货退款';
        }

        
        return [
            'id'=>$salesReturnApply->id,
            'user_id'=>$salesReturnApply->user_id,
            'order_id'=>$salesReturnApply->order_id,
            'type'=>$salesReturnApply->type,
            'type_name'=>$type_name,
            'cause'=>$salesReturnApply->cause,
            'cause_name'=>$cause,
            'describe'=>$salesReturnApply->describe,
            'pics'=>$salesReturnApply->pics,
            'pics_url'=>$pics_url,
            'status'=>$salesReturnApply->status,
            'status_name'=>$status,
            'express_type'=>$salesReturnApply->express_type,
            'express_no'=>$salesReturnApply->express_no,
            'created_at'=>$salesReturnApply->created_at,
        ];
    }

    public function includeUser(SalesReturnApply $salesReturnApply){
        return $this->item($salesReturnApply->user,new UserTransformer());
    }

    public function includeOrder(SalesReturnApply $salesReturnApply){
        return $this->item($salesReturnApply->order,new OrderTransformer());
    }

    public function includeOrderDetail(SalesReturnApply $salesReturnApply){
        return $this->collection($salesReturnApply->orderDetail,new OrderDetailTransformer());
    }
    
}
 