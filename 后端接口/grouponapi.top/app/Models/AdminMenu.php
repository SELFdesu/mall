<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMenu extends BaseModel
{
    use HasFactory;

    protected $fillable=[
        'name',
        'pid',
        'level'
    ];
    //分类的子类
    public function children(){
        return $this->hasMany(AdminMenu::class,'pid','id');
    }
}
