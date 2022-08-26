<?php
namespace App\Transformers;

use App\Models\ApplyHead;
use League\Fractal\TransformerAbstract;

class ApplyHeadTransformer extends TransformerAbstract{

    //可include的方法
    protected $availableIncludes=['user'];

    public function transform(ApplyHead $applyHead){
        
        //0:申请不通过 1:申请通过 2:用户提交申请，未处理
        if($applyHead->status==0){
            $status_name='申请不通过';
        }elseif($applyHead->status==1){
            $status_name='申请通过';
        }elseif($applyHead->status==2){
            $status_name='申请未处理';
        }

        return [
            'id'=>$applyHead->id,
            'user_id'=>$applyHead->user_id,
            'info_id'=>$applyHead->info_id,
            'apply_role_id'=>$applyHead->apply_role_id,
            'apply_role_name'=>'团长',
            'status'=>$applyHead->status,
            'status_name'=>$status_name,
            'created_at'=>$applyHead->created_at,
        ];
    }


    public function includeUser(ApplyHead $applyhead){
        return $this->item($applyhead->user,new UserTransformer());
    }
}
 