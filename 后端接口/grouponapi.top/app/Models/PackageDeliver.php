<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDeliver extends BaseModel
{
    use HasFactory;
    protected $fillable=[
        'head_id',
        'address_id',
        'pic',
        'store_name',
        'address_info',
        'status'
    ];

    public function order(){
        return $this->hasMany(Order::class, 'package_deliver_id','id');
    }

    public function headInfo(){
        return $this->belongsTo(HeadInfo::class, 'head_id','id');
    }
}
