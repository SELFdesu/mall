<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ApplyHeadInfo extends BaseModel
{
    use HasFactory;

    protected $fillable=[
        'pic',
        'applicant',
        'tel',
        'id_number',
        'identity',
        'store_name',
        'address',
        'store_address',
    ];

}
