<?php
namespace App\Transformers;

use App\Models\HeadApplyModify;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class HeadApplyModifyTransformer extends TransformerAbstract{

    protected $availableIncludes=['headInfo'];

    public function transform(HeadApplyModify $headApplyModify){
        if($headApplyModify->status==0){
            $status_name='拒绝';
        }elseif($headApplyModify->status==1) {
            $status_name='同意';
        }else{
            $status_name='未处理';
        }
        return [
            'id'=>$headApplyModify->id,
            'head_id'=>$headApplyModify->head_id,
            'pic'=>$headApplyModify->pic,
            'pic_url'=>oss_url($headApplyModify->pic,'delivery'),
            'applicant'=>$headApplyModify->applicant,
            'tel'=>$headApplyModify->tel,
            'id_number'=>$headApplyModify->id_number,
            'identity'=>$headApplyModify->identity,
            'store_name'=>$headApplyModify->store_name,
            'address'=>$headApplyModify->address,
            'address_name'=>get_region_name($headApplyModify->address),
            'store_address'=>$headApplyModify->store_address,
            'payment_method'=>$headApplyModify->payment_method,
            'payment_account'=>$headApplyModify->payment_account,
            'status'=>$headApplyModify->status,
            'status_name'=>$status_name,
        ];
    }
    
    public function includeHeadInfo(HeadApplyModify $headApplyModify){
        return $this->item($headApplyModify->headInfo,new HeadInfoTransformer());
    }
    
}
 