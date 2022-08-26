<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Transformers\UserListTransformer;
use App\Transformers\UserTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    /**
     * 获取用户列表
     */
    public function index(Request $request)
    {
        $request->validate([
            'locked'=>'In:0,1',
            'sex'=>'In:0,1',
            'time_begin'=>'date',
            'time_end'=>'date',
        ]);

        if(isset($request->time_begin)||isset($request->time_end)){
            if(!isset($request->time_begin)||!isset($request->time_end)){
                return $this->response->errorBadRequest('请输入完整的时间区间');
            }
            if(strtotime($request->time_begin)>strtotime($request->time_end)){
                return $this->response->errorBadRequest('时间范围错误');
            }       
        }

        $locked=$request->locked;
        $sex=$request->sex;
        $time_begin=$request->input('time_begin');
        $time_end=$request->input('time_end');

        $users=User::whereNotIn('id',[1])
        ->when(isset($request->locked),function($query) use($locked){
            $query->where('locked',$locked);
        })
        ->when(isset($request->sex),function($query) use($sex){
            $query->where('sex',$sex);
        })
        ->when(isset($request->time_begin),function($query) use($time_begin,$time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->latest()
        ->paginate();

        return $this->response->paginator($users,new UserListTransformer());
    }


    /**
     * 用户详情
     */
    public function show(User $user)
    {
        if($user->hasRole('admin')){
            return [];
        }
        return $this->response->item($user,new UserTransformer());
    }

    /**
     * 启用\封禁用户
     */
    public function isOn(User $user){
        if($user->hasRole('user')){
            //事务处理
            DB::beginTransaction();
            try{
                if($user->hasRole('head')&&!$user->locked){
                    $headInfo=$user->headInfo()->first();
                    $packageDeliver=$user->packageDeliver()->first();
                    $headInfo->status=0;
                    $packageDeliver->status=0;
                    $headInfo->save();
                    $packageDeliver->save();
                    $user->removeRole('head');
                }

                $user->locked=!$user->locked;
                $user->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                return $this->response->errorInternal('数据库操作失败！请联系管理员。');
            }
            return $this->response->created();
        }
        return $this->response->errorBadRequest('您无法对该用户进行封禁!');

    }


}
