<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;

class applyHeadRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       $idcardReg='/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/';
        return [
            'pic'=>'required',
            'applicant'=>'required|max:10',
            'tel'=>'required',
            'id_number'=>array('required',"regex:$idcardReg"),
            'identity'=>'required|in:0,1',
            'store_name'=>'required',
            'address'=>'required|exists:areas,id',
            'store_address'=>'required',
        ];
    }
}
