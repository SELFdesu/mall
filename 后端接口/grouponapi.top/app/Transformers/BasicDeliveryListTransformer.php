<?php
namespace App\Transformers;

use App\Models\PackageDeliver;
use League\Fractal\TransformerAbstract;

class BasicDeliveryListTransformer extends TransformerAbstract{
    public function transform(PackageDeliver $packageDeliver){
        return [
            'id'=>$packageDeliver->id,
            'pic'=>$packageDeliver->pic,
            'pic_url'=>oss_url($packageDeliver->pic,'delivery'),
            'store_name'=>$packageDeliver->store_name,
            'address_name'=>get_region_name($packageDeliver->address_id),
            'address_info'=>$packageDeliver->address_info
        ];
    }

    
}
 