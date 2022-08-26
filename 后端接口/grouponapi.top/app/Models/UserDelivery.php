<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDelivery extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'delivery_id',
    ];
}
