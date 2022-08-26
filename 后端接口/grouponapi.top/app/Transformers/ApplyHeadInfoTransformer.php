<?php
namespace App\Transformers;

use App\Models\ApplyHeadInfo;
use League\Fractal\TransformerAbstract;

class ApplyHeadInfoTransformer extends TransformerAbstract{
    public function transform(ApplyHeadInfo $applyHeadInfo){

        $address_id=$applyHeadInfo->address;

        if($applyHeadInfo->identity==0){
            $identity_name='便利店店长';
        }else{
            $identity_name='其他';
        }

        return [
            'id'=>$applyHeadInfo->id,
            'pic'=>$applyHeadInfo->pic,
            'pic_url'=>oss_url($applyHeadInfo->pic,'delivery'),
            'applicant'=>$applyHeadInfo->applicant,
            'tel'=>$applyHeadInfo->tel,
            'id_number'=>$applyHeadInfo->id_number,
            'identity'=>$applyHeadInfo->identity,
            'identity_name'=>$identity_name,
            'store_name'=>$applyHeadInfo->store_name,
            'address_id'=>$applyHeadInfo->address,
            'address_name'=>get_region_name($address_id),
            'store_address'=>$applyHeadInfo->store_address,
            'created_at'=>$applyHeadInfo->created_at,
        ];
    }

    
}
 