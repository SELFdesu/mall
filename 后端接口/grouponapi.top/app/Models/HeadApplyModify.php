<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadApplyModify extends Model
{
    use HasFactory;
    protected $fillable=[
        'head_id',
        'pic',
        'applicant',
        'tel',
        'id_number',
        'identity',
        'store_name',
        'address',
        'store_address',
        'payment_method',
        'payment_account',
        'status'
    ];
    public function headInfo(){
        return $this->belongsTo(HeadInfo::class,'head_id','id');
    }
}
