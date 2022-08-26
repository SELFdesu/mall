<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplyHead extends BaseModel
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'info_id',
        'apply_role_id',
        'status'
    ];


    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function headinfo(){
        return $this->belongsTo(ApplyHeadInfo::class,'info_id','id');
    }
}
