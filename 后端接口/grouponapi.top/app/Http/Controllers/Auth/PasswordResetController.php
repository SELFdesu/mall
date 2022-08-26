<?php

namespace App\Http\Controllers\Auth;

use App\Events\SendEmail;
use App\Events\SendSms;
use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;


class PasswordResetController extends BaseController
{

    //发送邮箱验证码
    public function sendEmailCode(Request $request){
        $request->validate([
            'email'=>'required|exists:users,email|email'
        ]);
        
        $email=$request->input('email');
        if(!User::where('email',$email)->exists()){
            return $this->response->errorBadRequest('该邮箱账号不存在！');
        }
        
        SendEmail::dispatch($email,2,15);
        
        return $this->response->noContent();
    }
    
    //验证用户输入的邮箱验证码并重置密码
    public function verifyEmailCode(Request $request){
        $request->validate([
            'email'=>'required|email',
            'code'=>'required',
            'newpassword'=>'required|confirmed',
        ]);

        $email=$request->input('email');
        $code=$request->input('code');
        $newpassword=$request->input('newpassword');
        if(!User::where('email',$email)->exists()){
            return $this->response->errorBadRequest('该邮箱账号不存在！');
        }

        if(!verify_email_captcha($email,$code,2)){
            return $this->response->error('验证码错误',422);
        }
        User::where('email',$email)->update([
            'password'=>bcrypt($newpassword)
        ]);
        return $this->response->created();

    }



    //发送手机验证码
    public function sendSmsCode(Request $request){
        $request->validate([
            'phone'=>'required|between:11,11'
        ]);
        $phone=$request->input('phone');
        $type='resetPassword';
        
        if(!User::where('tel',$phone)->exists()){
            return $this->response->errorBadRequest('该手机账号不存在！');
        }

        SendSms::dispatch($phone,$type);
        
        return $this->response->noContent();
    }

    //验证用户输入的手机验证码并重置密码
    public function verifySmsCode(Request $request){
        $request->validate([
            'phone'=>'required|between:11,11',
            'code'=>'required',
            'newpassword'=>'required|confirmed',
        ]);

        $phone=$request->input('phone');
        $code=$request->input('code');
        $type='resetPassword';
        $newpassword=$request->input('newpassword');
        if(!User::where('tel',$phone)->exists()){
            return $this->response->errorBadRequest('该手机账号不存在！');
        }

        if(!verify_phone_captcha($phone,$code,$type)){
            return $this->response->error('验证码错误',422);
        }
        User::where('tel',$phone)->update([
            'password'=>bcrypt($newpassword)
        ]);
        return $this->response->created();

    }
    
}
