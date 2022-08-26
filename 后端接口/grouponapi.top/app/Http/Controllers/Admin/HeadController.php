<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\ApplyHead;
use App\Models\ApplyHeadInfo;
use App\Models\HeadApplyModify;
use App\Models\HeadInfo;
use App\Models\HeadResign;
use App\Models\PackageDeliver;
use App\Models\Payroll;
use App\Models\Salary;
use App\Models\User;
use App\Transformers\ApplyHeadInfoTransformer;
use App\Transformers\ApplyHeadTransformer;
use App\Transformers\HeadApplyModifyListTransformer;
use App\Transformers\HeadApplyModifyTransformer;
use App\Transformers\HeadInfoTransformer;
use App\Transformers\HeadResignTransformer;
use App\Transformers\HeadTransformer;
use App\Transformers\PayRollTransformer;
use App\Transformers\SalaryTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeadController extends BaseController
{
    /**
     * 获取提交成为团长的申请列表
     */
    public function applyList(Request $request){
        $request->validate([
            'status'=>'In:0,1,2',
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

        $status=$request->input('status');
        $time_begin=$request->input('time_begin');
        $time_end=$request->input('time_end');

        $applyHead=ApplyHead::when(isset($request->status),function($query) use($status) {
            $query->where('status',$status);
        })
        ->when(isset($request->time_begin),function($query)use($time_begin, $time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->paginate(15);

        return $this->response->paginator($applyHead,new ApplyHeadTransformer());
    }

    /**
     * 团长申请搜索(用户id、申请编号)
     */
    public function serchApply(Request $request){
        $request->validate([
            'type'=>'required|In:user_id,apply_id',
            'number'=>'required'
        ]);
        $type=$request->input('type');
        $number=$request->input('number');

        $applyHead=ApplyHead::when($type=='user_id',function($query)use($number){
            $query->where('user_id',$number);
        })
        ->when($type=='apply_id',function($query)use($number){
            $query->where('id',$number);
        })->paginate(15);
        return $this->response->paginator($applyHead,new ApplyHeadTransformer());
    }
    
    /**
     * 获取申请人的申请信息
     */
    public function applyInfo(ApplyHeadInfo $applyHeadInfo){
       return $this->response->item($applyHeadInfo,new ApplyHeadInfoTransformer());
    }
    
    /**
     * 通过用户申请，批准成为团长
     */
    public function addHead(ApplyHead $applyHead){
        if($applyHead->status==1){
            return $this->response->errorBadRequest('该申请已通过！请勿重复请求。');
        }
        if($applyHead->status==0){
            return $this->response->errorBadRequest('该申请已退回！请勿重复请求。');
        }

        $applyHeadInfo=$applyHead->headinfo;
        $user=User::find($applyHead->user_id);
        
        if($user->hasRole('head')){
            return $this->response->errorBadRequest('该用户已经是团长了!');
        }

        //事务处理
        DB::beginTransaction();
        try{
            //赋予团长角色
            $user->assignRole('head');
            $applyHead->update(['status'=>'1']);

            //添加团长信息
            $head=HeadInfo::create([
                'user_id'=>$user->id,
                'name'=>$applyHeadInfo->applicant,
                'tel'=>$applyHeadInfo->tel,
                'id_number'=>$applyHeadInfo->id_number,
                'identity'=>$applyHeadInfo->identity,
            ]);
            PackageDeliver::create([
                'head_id'=>$head->id,
                'pic'=>$applyHeadInfo->pic,
                'store_name'=>$applyHeadInfo->store_name,
                'address_id'=>$applyHeadInfo->address,
                'address_info'=>$applyHeadInfo->store_address,
            ]);

            Salary::create([
                'head_id'=>$head->id,
                'salary'=>0,
                'status'=>1
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->response->errorInternal('数据库操作失败！请联系管理员。');
        }

        return $this->response->created();
    }

    /**
     * 拒绝申请团长请求
     */
    public function refuseHead(ApplyHead $applyHead){
        if($applyHead->status==1){
            return $this->response->errorBadRequest('该申请已通过！请勿重复请求。');
        }
        if($applyHead->status==0){
            return $this->response->errorBadRequest('该申请已退回！请勿重复请求。');
        }
        DB::beginTransaction();
        try{
            $applyHead->update(['status'=>'0']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->response->errorInternal('数据库操作失败！请联系管理员。');
        }
        return $this->response->created();
    }


    /**
     * 获取团长列表
     */
    public function headList(Request $request){
        $request->validate([
            'status'=>'In:0,1',
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


        $status=$request->input('status',1);
        $time_begin=$request->input('time_begin');
        $time_end=$request->input('time_end');

        $head=HeadInfo::when(isset($request->status),function($query)use($status){
            $query->where('status',$status);
        })
        ->when(isset($request->time_begin),function($query)use($time_begin, $time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->paginate(15);
        return $this->response->paginator($head,new HeadTransformer());
    }
    /**
     * 团长搜索(团长id、用户id、姓名、电话)
     */
    public function serchHead(Request $request){
        $request->validate([
            'type'=>'required|In:head_id,user_id,name,tel',
            'number'=>'required',
        ]);
        $type=$request->input('type');
        $number=$request->input('number');

        $head=HeadInfo::when($type=='head_id',function($query)use($number){
            $query->where('id',$number);
        })
        ->when($type=='user_id',function($query)use($number){
            $query->where('user_id',$number);
        })
        ->when($type=='name',function($query)use($number){
            $query->where('name',$number);
        })
        ->when($type=='tel',function($query)use($number){
            $query->where('tel',$number);
        })
        ->paginate(15);

        return $this->response->paginator($head,new HeadTransformer());
    }

    /**
     * 获取团长详情
     */
    public function headInfo(HeadInfo $headInfo){
        return $this->response->item($headInfo,new HeadInfoTransformer());
    }


    /**
     * 撤销团长身份
     */
    public function takeBackHead(User $user){
        if(!$user->hasRole('head')){
            return $this->response->errorBadRequest('该用户不是团长!');
        }

        $headResign=HeadResign::where('user_id',$user->id)->where('status',2)->where('type',0);

        //事务处理
        DB::beginTransaction();
        try{
            $user->removeRole('head');
            $head=HeadInfo::where('user_id',$user->id);
            PackageDeliver::where('head_id',$head->first()->id)->update(['status'=>0]);
            $head->update(['status'=>0]);

            if($headResign->exists()){
                $headResign->update([
                    'status'=>1
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return $this->response->errorInternal('数据库操作失败！请联系管理员。');
        }

        return $this->response->created();
    }

    /**
     * 恢复团长身份
     */
    public function recoverHead(User $user){
        if($user->hasRole('head')){
            return $this->response->errorBadRequest('该用户是团长!');
        }
        if(!HeadInfo::where('user_id',$user->id)->exists()){
            return $this->response->errorBadRequest('该用户没有申请过团长');
        }

        // $headResign=HeadResign::where('user_id',$user->id)->where('status',2)->where('type',1);

        //事务处理
        DB::beginTransaction();
        try{
            $user->assignRole('head');
            $head=HeadInfo::where('user_id',$user->id);
            PackageDeliver::where('head_id',$head->first()->id)->update(['status'=>1]);
            $head->update(['status'=>1]);

            // if($headResign->exists()){
            //     $headResign->updated([
            //         'status'=>1
            //     ]);
            // }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return $this->response->errorInternal('数据库操作失败！请联系管理员。');
        }

        return $this->response->created();

    }


    //申请辞去团长列表
    public function applyResignList(Request $request){
        $request->validate([
            'status'=>'In:0,1,2',
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

        $status=$request->input('status',1);
        $time_begin=$request->input('time_begin');
        $time_end=$request->input('time_end');

        $headResign=HeadResign::when(isset($request->status),function($query)use($status){
            $query->where('status',$status);
        })
        ->when(isset($request->time_begin),function($query)use($time_begin, $time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->paginate(15);
        return $this->response->paginator($headResign,new HeadResignTransformer());
    }


    //申请辞去团长详情
    public function applyResignInfo(HeadResign $headResign){
        return $this->response->item($headResign,new HeadResignTransformer());
    }
    //拒绝辞去请求
    public function applyResignRefuse(HeadResign $headResign){
        $headResign->update([
            'status'=>0
        ]);
        return $this->response->created();
    }

    //上月团长工资列表
    public function headSalary(Request $request){
        $request->validate([
            'status'=>'In:0,1',
        ]);

        $status=$request->input('status',1);

        $salary=Salary::when(isset($request->status),function($query) use($status) {
            $query->where('status',$status);
        })->paginate();
        return $this->response->paginator($salary,new SalaryTransformer());
    }

    //工资条
    public function headPayroll(Salary $salary){
        $payroll=Payroll::where('salary_id',$salary->id)
            ->latest()
            ->paginate();
        return $this->response->paginator($payroll,new PayRollTransformer());
    }


    //结算团长上月收益
    public function changeHeadSalaryStatus(Salary $salary){
        if($salary->status){
            return $this->response->errorBadRequest('该记录已处理！');
        }
        try{
            DB::beginTransaction();
            $salary->status=1;
            $salary->save();
            $last= strtotime("-1 month", time());
            Payroll::create([
                'head_id'=>$salary->head_id,
                'salary_id'=>$salary->id,
                'salary'=>$salary->salary,
                'payment_method'=>$salary->payment_method,
                'payment_account'=>$salary->payment_account,
                'status'=>1,
                'date'=>date('Y-m', $last)
            ]);
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
            return $this->response->errorInternal('数据库操作失败！请联系管理员。');
        }
        return $this->response->created();
    }

    //更改工资条状态
    public function changePayrollStatus(Payroll $payroll){
        if($payroll->status){
            return $this->response->errorBadRequest('该记录已处理！');
        }
        $salary=Salary::where('id',$payroll->salary_id)->first();
        $payroll->status=1;
        if($payroll->payment_method==''||$payroll->payment_account==''){
            $payroll->payment_method=$salary->payment_method;
            $payroll->payment_account=$salary->payment_account;
        }
        $payroll->save();
        return $this->response->created();
    }


    /**
     * 团长修改信息
     */
    //信息修改列表
    public function headModifyList(Request $request){
        $request->validate([
            'status'=>'In:0,1,2',
            'time_begin'=>'date',
            'time_end'=>'date',
        ]);

        $status=$request->input('status',1);
        $time_begin=$request->input('time_begin');
        $time_end=$request->input('time_end');

        if(isset($request->time_begin)||isset($request->time_end)){
            if(!isset($request->time_begin)||!isset($request->time_end)){
                return $this->response->errorBadRequest('请输入完整的时间区间');
            }
            if(strtotime($request->time_begin)>strtotime($request->time_end)){
                return $this->response->errorBadRequest('时间范围错误');
            }       
        }

        $head_apply_modify=HeadApplyModify::when(isset($request->status),function($query)use($status){
            $query->where('status',$status);
        })
        ->when(isset($request->time_begin),function($query)use($time_begin, $time_end){
            $query->whereBetween ('created_at',[$time_begin,$time_end]);
        })
        ->paginate();
        return $this->response->paginator($head_apply_modify,new HeadApplyModifyListTransformer());
    }
    //信息修改详情
    public function headModifyInfo(HeadApplyModify $headApplyModify){
        return $this->response->item($headApplyModify,new HeadApplyModifyTransformer());
    }
    //是否同意
    public function headModifyDispose(Request $request,HeadApplyModify $headApplyModify){
        $request->validate([
            'status'=>'required|In:0,1'
        ]);
        if($headApplyModify->status!=2){
            return $this->response->errorBadRequest('该请求已处理！');
        }
        
        if($request->input('status')){
            try{
                DB::beginTransaction();
                $headApplyModify->status=$request->input('status');
                $headApplyModify->save();
                HeadInfo::where('id',$headApplyModify->head_id)->update([
                    'name'=>$headApplyModify->applicant,
                    'tel'=>$headApplyModify->tel,
                    'id_number'=>$headApplyModify->id_number,
                    'identity'=>$headApplyModify->identity
                ]);
                PackageDeliver::where('head_id',$headApplyModify->head_id)->update([
                    'pic'=>$headApplyModify->pic,
                    'store_name'=>$headApplyModify->store_name,
                    'address_id'=>$headApplyModify->address,
                    'address_info'=>$headApplyModify->store_address,
                ]);
                Salary::where('head_id',$headApplyModify->head_id)->update([
                    'payment_method'=>$headApplyModify->payment_method,
                    'payment_account'=>$headApplyModify->payment_account,
                ]);
                DB::commit();
            } catch (Exception $e){
                DB::rollBack();
                return $this->response->errorInternal('数据库操作失败！请联系管理员。');
            }
        }else{
            $headApplyModify->status=$request->input('status');
            $headApplyModify->save();
        }
        return $this->response->created();
        
    }
}
