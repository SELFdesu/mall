<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends BaseModel
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'product_id',
        'content',
        'grade'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
    
    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
