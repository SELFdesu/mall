<?php
namespace App\Transformers;

use App\Models\HeadInfo;
use League\Fractal\TransformerAbstract;

class HeadTransformer extends TransformerAbstract{

    protected $availableIncludes=['user','packageDeliver'];

    public function transform(HeadInfo $headInfo){
        if($headInfo->identity==0){
            $identity_name='便利店店长';
        }else{
            $identity_name='其他';
        }
        return [
            'id'=>$headInfo->id,
            'userId'=>$headInfo->user_id,
            'name'=>$headInfo->name,
            'identity'=>$headInfo->identity,
            'identity_name'=>$identity_name,
            'tel'=>$headInfo->tel,
            'status'=>$headInfo->status,
        ];
    }

    public function includeUser(HeadInfo $headInfo){
        return $this->item($headInfo->user,new UserTransformer());
    }

    public function includePackageDeliver(HeadInfo $headInfo){
        return $this->item($headInfo->packageDeliver,new PackageDeliverTransformer());
    }

    
}
 