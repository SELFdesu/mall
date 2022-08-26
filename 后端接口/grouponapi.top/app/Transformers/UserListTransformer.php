<?php
namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserListTransformer extends TransformerAbstract{
    public function transform(User $user){
        return [
            'id'=>$user->id,
            'username'=>$user->username,
            'tel'=>$user->tel,
            'email'=>$user->email,
            'sex'=>$user->sex?'女':'男',
            'locked'=>$user->locked,
            'created_at'=>$user->created_at,
        ];
    }

    
}
 