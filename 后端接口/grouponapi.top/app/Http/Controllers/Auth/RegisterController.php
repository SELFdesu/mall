<?php

namespace App\Http\Controllers\Auth;

use App\Events\SendEmail;
use App\Events\SendSms;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;


class RegisterController extends BaseController
{
    /**
     * 邮箱注册
     */
    //发送邮箱验证码
    public function sendEmailCode(Request $request){
        $request->validate([
            'email'=>'required|email'
        ]);
        $email=$request->input('email');
        if(User::where('email',$email)->exists()){
            return $this->response->errorBadRequest('该邮箱账号已存在！');
        }

        SendEmail::dispatch($email,3,15);

        return $this->response->noContent();
    }
    //注册邮箱账号
    public function registerEmail(RegisterRequest $request)
    {
        $request->validate([
            'email'=>'required|email|unique:users,email',
            'code'=>'required'
        ]);

        $email=$request->input('email');
        $code=$request->input('code');
        if(User::where('email',$email)->exists()){
            return $this->response->errorBadRequest('该邮箱账号已存在！');
        }

        if(!verify_email_captcha($email,$code,3)){
            return $this->response->error('验证码错误',422);
        }

        //创建用户
        $user=User::create([
            'username'=>$request->input('username'),
            'email'=>$request->input('email'),
            'password'=>bcrypt($request->input('password')),
            'sex'=>$request->input('sex'),
        ]);
        //分配权限
        $user->assignRole('user');
        // 返回相created响应
        return $this->response->created();
    }


    /**
     * 手机号注册
     */
    //发送手机验证码
    public function sendSmsCode(Request $request){
        $request->validate([
            'phone'=>'required|between:11,11'
        ]);
        $phone=$request->input('phone');
        $type='register';

        if(User::where('tel',$phone)->exists()){
            return $this->response->errorBadRequest('该手机账号已存在！');
        }
        
        SendSms::dispatch($phone,$type);
        
        return $this->response->noContent();
    }

    //注册手机账号
    public function registerPhone(RegisterRequest $request)
    {
        $request->validate([
            'phone'=>'required|unique:users,tel|between:11,11',
            'code'=>'required'
        ]);
        $phone=$request->input('phone');
        $code=$request->input('code');
        $type='register';
        
        if(!verify_phone_captcha($phone,$code,$type)){
            return $this->response->error('验证码错误',422);
        }

        if(User::where('tel',$phone)->exists()){
            return $this->response->errorBadRequest('该手机账号已存在！');
        }

        //创建用户
        $user=User::create([
            'username'=>$request->input('username'),
            'tel'=>$phone,
            'password'=>bcrypt($request->input('password')),
            'sex'=>$request->input('sex'),
        ]);
        //分配权限
        $user->assignRole('user');
        // 返回相created响应
        return $this->response->created();
    }
}
