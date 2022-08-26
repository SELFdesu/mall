<?php
namespace App\Transformers;

use App\Models\HeadInfo;
use App\Models\HeadResign;
use League\Fractal\TransformerAbstract;

class HeadResignTransformer extends TransformerAbstract{

    protected $availableIncludes=['headInfo'];

    public function transform(HeadResign $headResign){

        if($headResign->status==0){
            $status_name='拒绝';
        }elseif($headResign->status==1){
            $status_name='同意';
        }elseif($headResign->status==2){
            $status_name='待处理';
        }

        return [
            'id'=>$headResign->id,
            'cause'=>$headResign->cause,
            'type'=>$headResign->type,
            'type_name'=>$headResign->type?'复职':'离职',
            'status'=>$headResign->status,
            'status_name'=>$status_name
        ];
    }

    public function includeHeadInfo(HeadResign $headResign){
        return $this->item($headResign->headInfo,new HeadInfoTransformer());
    }

    
}
 