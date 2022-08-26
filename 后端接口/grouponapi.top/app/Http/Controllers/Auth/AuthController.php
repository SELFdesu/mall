<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    // 获取验证码及key
    public function getCaptcha(){
        return $this->response->array([
            'status_code'=>200,
            'message'=>'created succeed',
            'url'=>app('captcha')->create('default',true)
        ]);
    }


    /**
     * 普通用户登录
     */
    public function login(LoginRequest $request)
    {
        $request->validate([
            'account'=>'required'
        ]);
        $account=$request->input('account');
        $password=$request->input('password');
        $captcha=$request->input('captcha');
        $captcha_key=$request->input('captcha_key');

        if (!captcha_api_check($captcha, $captcha_key)){
            return $this->response->errorBadRequest('验证码不匹配！');
        }

        //验证传入的是否为邮箱格式
        if (filter_var($account, FILTER_VALIDATE_EMAIL)) {
            if(!User::where('email',$account)->exists()){
                return $this->response->errorBadRequest('该邮箱账号不存在！');
            }
            if (!$token = auth('api')->attempt(['email'=>$account,'password'=>$password])) {
                return $this->response->errorUnauthorized();
            }
        }else{
            if(!User::where('tel',$account)->exists()){
                return $this->response->errorBadRequest('该手机账号不存在！');
            }
            if (!$token = auth('api')->attempt(['tel'=>$account,'password'=>$password])) {
                return $this->response->errorUnauthorized();
            }
        }
        //验证用户身份,还需创建一个管理员登录的路由和控制器判断身份
        $user=auth('api')->user();
        if(!$user->hasRole('user')||$user->locked){
            abort(403);
        }
        //返回token
        return $this->respondWithToken($token);
    }

    /**
     * 网站管理员登录
     */
    public function adminLogin(LoginRequest $request)
    {
        $account=$request->input('account');
        $password=$request->input('password');
        $captcha=$request->input('captcha');
        $captcha_key=$request->input('captcha_key');
        
        if (!captcha_api_check($captcha, $captcha_key)){
            return $this->response->errorBadRequest('验证码不匹配！');
        }
        
        //验证传入的是否为邮箱格式
        if (filter_var($account, FILTER_VALIDATE_EMAIL)) {
            if (!$token = auth('api')->attempt(['email'=>$account,'password'=>$password])) {
                return $this->response->errorUnauthorized();
            }
        }else{
            if (!$token = auth('api')->attempt(['tel'=>$account,'password'=>$password])) {
                return $this->response->errorUnauthorized();
            }
        }

        //验证用户身份
        $user=auth('api')->user();
        if(!$user->hasRole('admin')){
            abort(403);
        }
        
        return $this->respondWithToken($token);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        auth('api')->logout();
        return $this->response->noContent();
    }

    /**
     * 获取当前用户信息
     */
    public function me(){

        $user=auth('api')->user();
        return $this->response->item($user,new UserTransformer());

    }

    /**
     * 修改用户头像
     */
    public function modifyAvatar(Request $request){
        $request->validate([
            'avatar'=>'required'
        ]);
        $user=auth('api')->user();
        $user->update([
            'avatar'=>$request->input('avatar')
        ]);
        return $this->response->created();
    }

    /**
     * 刷新token
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * 设置token返回格式
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60000
        ]);
    }

}