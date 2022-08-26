<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadResign extends Model
{
    use HasFactory;

    protected $fillable=[
        'head_id',
        'user_id',
        'cause',
        'type',
        'status',
    ];

    public function headInfo(){
        return $this->belongsTo(HeadInfo::class, 'head_id','id');
    }
}
