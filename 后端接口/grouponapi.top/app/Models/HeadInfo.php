<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadInfo extends BaseModel
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'name',
        'tel',
        'id_number',
        'identity',
        'package_deliver_id',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function packageDeliver(){
        return $this->hasOne(PackageDeliver::class,'head_id','id');
    }

}
