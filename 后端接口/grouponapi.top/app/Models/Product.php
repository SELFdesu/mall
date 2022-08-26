<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends BaseModel
{
    use HasFactory;

    protected $fillable=[
        'title',
        'picture',
        'pics',
        'category_id',
        'price',
        'stock',
        'commentnum',
        'sales',
        'description',
        'attribute',
    ];
    
    // protected $casts=[
    //     'pics'=>'array',
    //     'attribute'=>'array',
    // ];
    public function comment(){
        return $this->hasMany(Comment::class,'product_id','id');
    }

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }

}
