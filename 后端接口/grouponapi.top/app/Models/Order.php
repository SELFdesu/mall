<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends BaseModel
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'package_deliver_id',
        'express_type',
        'express_no',
        'pay_time',
        'mode_of_payment',
        'payment_no',
        'signfor_time',
        'status',
        'amount',
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
    public function orderDetail(){
        return $this->hasMany(OrderDetail::class,'order_id','id');
    }
    public function salesReturnApply(){
        return $this->hasOne(SalesReturnApply::class,'order_id','id');
    }
}
