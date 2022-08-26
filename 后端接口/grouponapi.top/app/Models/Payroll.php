<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable=[
        'head_id',
        'salary_id',
        'salary',
        'payment_method',
        'payment_account',
        'status',
        'date'
    ];
}
