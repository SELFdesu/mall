<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;
    protected $fillable=[
        'head_id',
        'salary',
        'payment_method',
        'payment_account',
        'status'
    ];
    public function payroll(){
        return $this->hasMany(Payroll::class,'salary_id','id');
    }
    
    public function headInfo(){
        return $this->belongsTo(HeadInfo::class,'head_id','id');
    }
}
