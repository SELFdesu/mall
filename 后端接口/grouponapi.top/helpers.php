<?php

use App\Models\Category;
use App\Models\Product;
use Cblink\Region\Region;
use Illuminate\Support\Facades\Redis;

//获取商品分类列表
if(!function_exists("categoryList")){
    function categoryList($queryall=0,$onlyOne=0){
        $list=Category::select(['id','name','pic','pid','status','level'])
            ->where('pid',0)
            ->when($queryall==0,function($query) {
                $query->where('status',1);
            })
            ->when(!$onlyOne,function($query) use($queryall) {
                $query->with([
                    'children'=>function($query) use($queryall) {
                        $query->select(['id','name','pic','pid','status','level'])
                        ->when(!$queryall,function($query){
                            $query->where('status',1);
                        });
                    }
                ]);
            })
            ->get();
            foreach($list as $val){
                $val['pic_url']=oss_url($val['pic'],'category');
                if($queryall==1){
                    foreach($val['children'] as $child){
                        $child['pic_url']=oss_url($child['pic'],'category');
                    }
                }
                
            }

        return $list;
    }
}


//缓存所有一级启用分类
if(!function_exists("cache_category_onlyOne")){
    function cache_category_onlyOne(){
        return cache()->rememberForever('cache_category_onlyOne', function (){
            return categoryList(0,1);
        });
    }
}

//缓存所有启用的分类
if(!function_exists("cache_category")){
    function cache_category(){
        return cache()->rememberForever('cache_category', function (){
            return categoryList(0);
        });
    }
}


//缓存所有分类
if(!function_exists("cache_category_all")){
    function cache_category_all(){
        return cache()->rememberForever('cache_category_all', function () {
            return categoryList(1);
        });
    }
}

//清空分类缓存
if(!function_exists("forget_cache_category")){
    function forget_cache_category(){
        cache()->forget('cache_category_onlyOne');
        cache()->forget('cache_category');
        cache()->forget('cache_category_all');
    }
}

//验证传入的pics信息json格式
if(!function_exists("check_pics_json")){
    function check_pics_json($picsJson){
        if(!isset($picsJson->urls)){
            return 'json格式有误,urls项缺失';
        }elseif(!is_array($picsJson->urls)){
            return 'json格式有误,urls项应为一个数组';
        }
        return 0;
    }
}


//验证传入的attribute信息json格式
if(!function_exists("check_attribute_json")){
    function check_attribute_json($attributeJson){
        if(!isset($attributeJson->brand)){
            return 'json格式有误,brand项缺失';
        }elseif(!isset($attributeJson->place)){
            return 'json格式有误,place项缺失';
        }elseif(!isset($attributeJson->size)){
            return 'json格式有误,size项缺失';
        }elseif(!isset($attributeJson->production_date)){
            return 'json格式有误,production_date项缺失';
        }elseif(!isset($attributeJson->shelf_life)){
            return 'json格式有误,shelf_life项缺失';
        }elseif(!isset($attributeJson->storage_method)){
            return 'json格式有误,storage_method项缺失';
        }
        return 0;
    }
}

//验证订单结算时商品json格式
if(!function_exists("check_orderProduct_json")){
    function check_orderProduct_json($products_json){
         //处理传来的json字符串
        if(!isset($products_json->data)){
            return '缺少data项';
        }
        foreach($products_json->data as $val){

            //验证json格式
            if(!isset($val->product_id)){
                return '缺少product_id项';
            }elseif(!isset($val->quantity)){
                return '缺少quantity项';
            }
            
            //判断购买商品情况，是否有库存以及是否上架
            $oneProduct=Product::find($val->product_id);
            if($oneProduct==null){
                return '商品不存在！';
            }
            if($oneProduct->stock<=0){
                return '商品 '."$oneProduct->title".' 已售空！';
            }
            if(($oneProduct->stock-$val->quantity)<0){
                return '商品 '."$oneProduct->title".' 库存量不足！';
            }
            if(!$oneProduct->is_on){
                return '商品 '."$oneProduct->title".' 已下架！';
            }
        }

        return 1;
    }
}




//验证邮箱验证码
if(!function_exists('verify_email_captcha')){
    function verify_email_captcha($email,$code,$type){
        switch($type){
            case 1:
                $name='bind_email_';
                break;
            case 2:
                $name='resetpassword_email_';
                break;
            case 3:
                $name='register_email_';
                break;
            default:
                return 0;
        }
        
        // if($code==cache($name.$email)){
        //     cache()->forget($name.$email);
        //     return 1;
        // }
        if($code==Redis::get($name.$email)){
            // cache()->forget($name.$email);
            Redis::del($name.$email);
            return 1;
        }
        return 0;
    }

}

//验证手机验证码
if(!function_exists('verify_phone_captcha')){
    function verify_phone_captcha($phone,$code,$type){

        if($code==Redis::get($type.'smsCode'.$phone)){
            Redis::del($type.'smsCode'.$phone);
            return 1;
        }
        return 0;
    }

}

//处理oss地址（avatar，product，carousel）
if(!function_exists('oss_url')){
    function oss_url($key,$type=0){
        if($type==='avatar'){

            if(empty($key)){
                return config('filesystems')['disks']['oss']['bucket_url'].'/avatar/'.'default.jpg';
            }
            return config('filesystems')['disks']['oss']['bucket_url'].'/avatar/'.$key;

        }elseif($type==='product'){

            if(empty($key)){
                return config('filesystems')['disks']['oss']['bucket_url'].'/product_picture/'.'default.jpg';
            }
            return config('filesystems')['disks']['oss']['bucket_url'].'/product_picture/'.$key;

        }elseif($type==='carousel'){

            if(empty($key)){
                return config('filesystems')['disks']['oss']['bucket_url'].'/carousel/'.'default.jpg';
            }
            return config('filesystems')['disks']['oss']['bucket_url'].'/carousel/'.$key;
            
        }elseif($type==='category'){

            if(empty($key)){
                return config('filesystems')['disks']['oss']['bucket_url'].'/category/'.'default.jpg';
            }
            return config('filesystems')['disks']['oss']['bucket_url'].'/category/'.$key;
            
        }elseif($type==='delivery'){

            if(empty($key)){
                return config('filesystems')['disks']['oss']['bucket_url'].'/delivery/'.'default.jpg';
            }
            return config('filesystems')['disks']['oss']['bucket_url'].'/delivery/'.$key;
            
        }elseif($type==='sales_return'){

            if(empty($key)){
                return config('filesystems')['disks']['oss']['bucket_url'].'/sales_return/'.'default.jpg';
            }
            return config('filesystems')['disks']['oss']['bucket_url'].'/sales_return/'.$key;
            
        }else{
            return config('filesystems')['disks']['oss']['bucket_url'].'/'.'error.jpg';
        }
    }
}

//根据地区id获取地区名
if(!function_exists('get_region_name')){
    function get_region_name ($address_id){
        $region = new Region();
        $address_object=$region->nestFromChild($address_id);

        if(isset($address_object[0]->parent->parent->name)){
            $province=$address_object[0]->parent->parent->name;
        }else{
            $province='';
        }
        
        $city=$address_object[0]->parent->name;
        $county=$address_object[0]->name;
        return $province.' '.$city.' '.$county;
    }
}

//订单时间
if(!function_exists('get_deliver_time')){
    function get_deliver_time(){
        return date("Y-m-d 19:00:00");
    }
}

//验证商品评论json
if(!function_exists('check_comment_json')){
    function check_comment_json($comments){
        if(!isset($comments->data)){
            return 'json格式有误,data项缺失';
        }
        foreach($comments->data as $val){
            if(!isset($val->product_id)){
                return 'json格式有误,product_id项缺失';
            }elseif(!isset($val->content)){
                return 'json格式有误,content项缺失';
            }elseif(!isset($val->grade)){
                return 'json格式有误,grade项缺失';
            }
        }

        return 0;
    }
}
