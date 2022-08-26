<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnApply extends BaseModel
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'order_id',
        'type',
        'cause',
        'describe',
        'pics',
        'status',
        'express_type',
        'express_no'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id','id');
    }  

    public function orderDetail(){
        return $this->hasManyThrough('App\Models\OrderDetail','App\Models\Order','id','order_id','order_id','id');
    }
}
