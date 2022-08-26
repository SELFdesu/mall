<?php

namespace App\Http\Controllers\Auth;

use App\Events\SendEmail;
use App\Events\SendSms;
use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;

class BindController extends BaseController
{
    /**
     * 邮箱绑定
     */
    //发送邮箱验证码
    public function sendEmailCode(Request $request){
        $request->validate([
            'email'=>'required'
        ]);
        $email=$request->input('email');
        if(User::where('email',$email)->exists()){
            return $this->response->errorBadRequest('该邮箱账号已存在！');
        }
        
        SendEmail::dispatch($email,1,15);

        return $this->response->noContent();
    }
    
    // 验证邮箱验证码并绑定邮箱
    public function verifyEmailCode(Request $request){
        $request->validate([
            'email'=>'required',
            'code'=>'required'
        ]);
        $email=$request->input('email');
        $code=$request->input('code');
        if(User::where('email',$email)->exists()){
            return $this->response->errorBadRequest('该邮箱账号已存在！');
        }

        if(!verify_email_captcha($email,$code,1)){
            return $this->response->error('验证码错误',422);
        }

        $user=auth('api')->user();
        User::where('id',$user->id)->update([
            'email'=>$email
        ]);
        return $this->response->created();

    }


    /**
     * 手机绑定
     */
    //发送手机验证码
    public function sendSmsCode(Request $request){
        $request->validate([
            'phone'=>'required|between:11,11'
        ]);
        $phone=$request->input('phone');
        $type='bind';
        
        if(User::where('tel',$phone)->exists()){
            return $this->response->errorBadRequest('该手机账号已存在！');
        }

        SendSms::dispatch($phone,$type);
        
        return $this->response->noContent();
    }

    // 验证验证码
    public function verifySmsCode(Request $request){
        $request->validate([
            'phone'=>'required|between:11,11',
            'code'=>'required'
        ]);
        $phone=$request->input('phone');
        $code=$request->input('code');
        $type='bind';
        if(User::where('tel',$phone)->exists()){
            return $this->response->errorBadRequest('该手机账号已存在！');
        }

        if(!verify_phone_captcha($phone,$code,$type)){
            return $this->response->error('验证码错误',422);
        }

        $user=auth('api')->user();
        User::where('id',$user->id)->update([
            'tel'=>$phone
        ]);
        return $this->response->created();
    }
}
