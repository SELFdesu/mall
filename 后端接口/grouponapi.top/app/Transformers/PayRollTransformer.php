<?php
namespace App\Transformers;

use App\Models\Payroll;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class PayRollTransformer extends TransformerAbstract{
    public function transform(Payroll $payroll){
        return [
            'id'=>$payroll->id,
            'head_id'=>$payroll->head_id,
            'salary'=>$payroll->salary,
            'payment_method'=>$payroll->payment_method,
            'payment_account'=>$payroll->payment_account,
            'status'=>$payroll->status?'已处理':'未处理',
            'date'=>$payroll->date,
        ];
    }

    
}
 