<?php
namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract{
    public function transform(User $user){
        if($user->hasRole('head')){
            $userRole='团长';
        }elseif($user->hasRole('user')){
            $userRole='用户';
        }elseif($user->hasRole('admin')){
            $userRole='网站管理员';
        }else{
            $userRole='用户角色错误';
        }
        return [
            'id'=>$user->id,
            'username'=>$user->username,
            'avatar'=>$user->avatar,
            'avatar_url'=>oss_url($user->avatar,'avatar'),
            'userRole'=>$userRole,
            'tel'=>$user->tel,
            'email'=>$user->email,
            'sex'=>$user->sex?'女':'男',
            'locked'=>$user->locked,
            'created_at'=>$user->created_at,
        ];
    }

    
}
 