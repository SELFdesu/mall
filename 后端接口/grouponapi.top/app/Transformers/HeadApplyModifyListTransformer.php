<?php
namespace App\Transformers;

use App\Models\HeadApplyModify;
use App\Models\HeadInfo;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class HeadApplyModifyListTransformer extends TransformerAbstract{

    protected $availableIncludes=['headInfo'];

    public function transform(HeadApplyModify $headApplyModify){
        if($headApplyModify->status==0){
            $status_name='拒绝';
        }elseif($headApplyModify->status==1){
            $status_name='同意';
        }else{
            $status_name='未处理';
        }
        return [
            'id'=>$headApplyModify->id,
            'status'=>$headApplyModify->status,
            'status_name'=>$status_name,
        ];
    }

    public function includeHeadInfo(HeadApplyModify $headApplyModify){
        return $this->item($headApplyModify->headInfo,new HeadInfoTransformer());
    }

    
}
 