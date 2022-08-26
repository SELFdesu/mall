<?php
namespace App\Transformers;

use App\Models\Carousel;
use League\Fractal\TransformerAbstract;

class CarouselTransformer extends TransformerAbstract{
    public function transform(Carousel $carousel){
        return [
            'id'=>$carousel->id,
            'name'=>$carousel->name,
            'img'=>$carousel->img,
            'img_url'=>oss_url($carousel->img,'carousel'),
            'url'=>$carousel->url,
            'status'=>$carousel->status,
        ];
    }

    
}
 