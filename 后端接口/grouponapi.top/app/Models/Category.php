<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel
{
    use HasFactory;

    protected $fillable=[
        'name',
        'pic',
        'pid',
        'level',
    ];
    //分类的子类
    public function children(){
        return $this->hasMany(Category::class,'pid','id');
    }
}
