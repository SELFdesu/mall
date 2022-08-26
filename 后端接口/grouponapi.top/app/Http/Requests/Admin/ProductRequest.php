<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class ProductRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'=>'required',
            'picture'=>'required',
            'pics'=>'required|json',
            'category_id'=>'required|exists:categories,id',
            'price'=>'required|min:0',
            'stock'=>'required',
            'description'=>'required',
            'attribute'=>'required|json',
        ];
    }
}
