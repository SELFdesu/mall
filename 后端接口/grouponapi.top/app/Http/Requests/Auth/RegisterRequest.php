<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class RegisterRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username'=>'required|max:16',
            'password'=>'required|min:8|confirmed',
            'sex'=>'required|boolean',
            'code'=>'required'
        ];
    }
}
