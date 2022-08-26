<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\BindController;
use App\Http\Controllers\Auth\OssController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;

$api = app('Dingo\Api\Routing\Router');
$params=[
    'middleware'=>[
        'api.throttle',
        'bindings',
    ],
    'limit'=>120,
    'expires'=>1,
];

$api->version('v1',$params, function ($api) {
    $api->group(['prefix'=>'auth'],function($api){
        /**
         * 普通用户邮箱注册
         */
        //发送邮箱验证码
        $api->post('register/email/send',[RegisterController::class,'sendEmailCode']);
        //验证邮箱验证码并注册邮箱账号
        $api->post('register/email',[RegisterController::class,'registerEmail']);


        /**
         * 普通用户手机注册
         */
        // 获取手机验证码
        $api->post('register/phone/send',[RegisterController::class,'sendSmsCode']);
        // 验证手机验证码并注册手机账号
        $api->post('register/phone',[RegisterController::class,'registerPhone']);


        /**
         * 重置密码(邮箱)
         */
        //发送邮箱验证码
        $api->post('resetpassword/email/send',[PasswordResetController::class,'sendEmailCode']);
        //验证用户输入的邮箱验证码并重置密码
        $api->patch('resetpassword/email',[PasswordResetController::class,'verifyEmailCode']);
        
        /**
         * 重置密码(手机)
         */
        //发送手机验证码
        $api->post('resetpassword/phone/send',[PasswordResetController::class,'sendSmsCode']);
        //验证用户输入的手机验证码并重置密码
        $api->patch('resetpassword/phone',[PasswordResetController::class,'verifySmsCode']);


        /**
         * 登录
         */
        //获取验证码
        $api->get('captcha',[AuthController::class,'getCaptcha']);
        
        //用户登录
        $api->post('login',[AuthController::class,'login']);

        //网站管理员登录
        $api->post('adminlogin',[AuthController::class,'adminLogin']);

        //刷新token
        $api->post('refresh',[AuthController::class,'refresh']);

        //需要登录的路由
        $api->group(['middleware'=>['api.auth','check.permission']],function($api){
            /**
             * 获取登录用户信息
             */
            $api->get('me',[AuthController::class,'me'])->name('auth.me');

            /**
             * 修改用户头像
             */
            $api->patch('avatar/modify',[AuthController::class,'modifyAvatar'])->name('auth.changeavatar');

            /**
             * 绑定邮箱
             */
            //发送邮箱验证码
            $api->post('bind/email/send',[BindController::class,'sendEmailCode'])->name('auth.bindemailsend');
            //验证用户输入的邮箱验证码并绑定
            $api->patch('bind/email',[BindController::class,'verifyEmailCode'])->name('auth.bindemail');

            /**
             * 绑定手机
             */
            //发送手机验证码
            $api->post('bind/phone/send',[BindController::class,'sendSmsCode'])->name('auth.bindphonesend');
            //验证用户输入的手机验证码并绑定
            $api->patch('bind/phone',[BindController::class,'verifySmsCode'])->name('auth.bindphone');


            //oss的token
            $api->get('oss/token',[OssController::class,'token'])->name('oss.token');
            
            //退出
            $api->post('logout',[AuthController::class,'logout'])->name('auth.logout');
        });

    });
});
