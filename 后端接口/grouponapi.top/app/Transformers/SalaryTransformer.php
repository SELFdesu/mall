<?php
namespace App\Transformers;

use App\Models\Salary;
use League\Fractal\TransformerAbstract;

class SalaryTransformer extends TransformerAbstract{
    
    protected $availableIncludes=['headInfo'];

    public function transform(Salary $salary){
        return [
            'id'=>$salary->id,
            'head_id'=>$salary->head_id,
            'salary'=>$salary->salary,
            'payment_method'=>$salary->payment_method,
            'payment_account'=>$salary->payment_account,
            'status'=>$salary->status?'已处理':'未处理',
        ];
    }
    
    public function includeHeadInfo(Salary $salary){
        return $this->item($salary->headInfo,new HeadInfoTransformer());
    }

    
}
 