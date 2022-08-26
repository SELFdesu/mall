<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\applyHeadRequest;
use App\Mail\ArrivalNotice;
use App\Mail\EmailCaptchaPost;
use App\Models\ApplyHead;
use App\Models\ApplyHeadInfo;
use App\Models\HeadApplyModify;
use App\Models\HeadInfo;
use App\Models\HeadResign;
use App\Models\Order;
use App\Models\PackageDeliver;
use App\Models\Payroll;
use App\Models\Salary;
use App\Transformers\OrderTransformer;
use App\Transformers\PayRollTransformer;
use Cblink\Region\Area;
use Cblink\Region\Region;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use SebastianBergmann\Environment\Console;

class HeadController extends BaseController
{
    //申请成为团长
    public function applyHead(applyHeadRequest $request){
        $user=auth('api')->user();
        if($user->hasRole('head')){
            return $this->response->errorBadRequest('重复的请求');
        }
        
        $headCheck=HeadInfo::where('user_id',$user->id);

        if($headCheck->exists()){
            $salaryCheck=Salary::where('head_id',$headCheck->first()->id);
            if($salaryCheck->exists()){
                return $this->response->errorBadRequest('如需复职请联系管理员!');
            }
        }

        $address_level=Area::find($request->input('address'));
        $region = new Region();
        $nowCityChild=$region->nest($address_level->id);

        if($address_level->type==1){
            return $this->response->errorBadRequest('地区错误,应填写市\区\县级地区！');
        }elseif($address_level->type==2){
            if(count($nowCityChild[0]->children)){
                return $this->response->errorBadRequest('地区错误,应填写区\县级地区！');
            }
        }

        $applyHead=ApplyHead::where('user_id',$user->id);
        if($applyHead->exists()){
            if($applyHead->first()->status==0){
                DB::beginTransaction();
                try{
                    $applyHead->update(['status'=>2]);
                    $applyHead->first()
                        ->headinfo()
                        ->update([
                            'pic'=>$request->input('pic'),
                            'applicant'=>$request->input('applicant'),
                            'tel'=>$request->input('tel'),
                            'id_number'=>$request->input('id_number'),
                            'identity'=>$request->input('identity'),
                            'store_name'=>$request->input('store_name'),
                            'address'=>$request->input('address'),
                            'store_address'=>$request->input('store_address'),
                        ]);
                    DB::commit();

                } catch (Exception $e) {
                    DB::rollback();
                    return $this->response->errorInternal('数据库操作失败！请联系管理员。');
                }

                return $this->response->created();

            }elseif($applyHead->first()->status==2){
                return $this->response->errorInternal('您的申请正在处理中请耐心等待！');
            }else{
                return $this->response->errorBadRequest('如有需要请申请恢复团长身份！');
            }
        }
        
        DB::beginTransaction();
        try{
            $info=ApplyHeadInfo::create([
                'pic'=>$request->input('pic'),
                'applicant'=>$request->input('applicant'),
                'tel'=>$request->input('tel'),
                'id_number'=>$request->input('id_number'),
                'identity'=>$request->input('identity'),
                'store_name'=>$request->input('store_name'),
                'address'=>$request->input('address'),
                'store_address'=>$request->input('store_address'),
            ]);
            ApplyHead::create([
                'user_id'=>$user->id,
                'info_id'=>$info->id,
                'apply_role_id'=>2
            ]);
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            return $this->response->errorInternal('数据库操作失败！请联系管理员。');
        }

        return $this->response->created();
    }


    //用户未签收订单列表 
    public function orderList(){
        $user=auth('api')->user();
        $head_id=$user->headInfo()
            ->select('id')
            ->first()
            ->id;

        $package_deliver_id=PackageDeliver::select('id')
            ->where('head_id',$head_id)
            ->first()
            ->id;

        $orders=Order::whereIn('status',[3,4,5])
            ->where('package_deliver_id',$package_deliver_id)
            ->paginate();

        return $this->response->paginator($orders,new OrderTransformer());
    }


    //货物信息列表
    public function cargoList(){
        $user=auth('api')->user();
        $head_id=$user->headInfo()
            ->select('id')
            ->first()
            ->id;

        $package_deliver_id=PackageDeliver::select('id')
            ->where('head_id',$head_id)
            ->first()
            ->id;

        $order=Order::where('status',3)
            ->where('package_deliver_id',$package_deliver_id)
            ->get();

        $orderDetailArr=[];
        foreach($order as $val){
            array_push($orderDetailArr,$val->orderDetail()
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->select('product_id','quantity','products.title','products.picture')
                ->get()
            );
        }
        $countProduct=[];
        foreach($orderDetailArr as $val){
            foreach($val as $innerVal){
                if(!isset($countProduct[$innerVal->product_id])){
                    $data=[
                        "product_id"=>$innerVal->product_id,
                        "title"=>$innerVal->title,
                        "picture"=>$innerVal->picture,
                        "picture_url"=>oss_url($innerVal->picture,'product'),
                        "quantity"=>$innerVal->quantity,
                    ];
                    $countProduct[$innerVal->product_id]=$data;

                }else{
                    $countProduct[$innerVal->product_id]["quantity"]+=$innerVal->quantity;
                }
            }
        }
        $dispose_count=[];
        foreach($countProduct as $val){
            array_push($dispose_count,$val);
        }
        return $this->response->array($dispose_count);
    }

    /**
     * 确认签收货物
     */
    public function cargoSignFor(){
        $user=auth('api')->user();

        $package_deliver=$user->packageDeliver()->first();
        $order=Order::where('status',3)->where('package_deliver_id',$package_deliver->id)->get();
        $emailArr=[];
        foreach($order as $val){
            if($val->user()->first()->email!=''){
                array_push($emailArr,$val->user()->first()->email);
            }
        }

        DB::beginTransaction();
        try{
            foreach($order as $val){
                $val->status=4;
                $val->save();
            }
            if(count($emailArr)>0){
                Mail::to($emailArr)->queue(new ArrivalNotice($package_deliver->store_name));
            }
           
            DB::commit();

        } catch (Exception $e) {

            DB::rollback();
            return $this->response->errorInternal('数据库操作失败！请联系管理员。');
        }
        
        return $this->response->created();
    }


    //申请辞去团长身份
    public function applyResign(Request $request){
        $request->validate([
            'cause'=>'required'
        ]);
        $user=auth('api')->user();
        if(!$user->hasRole('head')){
            return $this->response->errorBadRequest('您不是团长！');
        }
        $headResign=HeadResign::where('user_id',$user->id);
        
        if($headResign->exists()){

            if($headResign->first()->status==2){
                if($headResign->first()->type==0){
                    return $this->response->errorBadRequest('您的离职申请正在处理中请勿重复请求！');
                }else{
                    return $this->response->errorBadRequest('您的恢复团长身份申请正在处理中请勿重复请求！');
                }
            }else{
                $headResign->update([
                    'cause'=>$request->input('cause'),
                    'type'=>0,
                    'status'=>2
                ]);
                return $this->response->created();
            }

        }
        
        $headId=$user->headInfo()->first()->id;
        HeadResign::create([
            'head_id'=>$headId,
            'user_id'=>$user->id,
            'cause'=>$request->input('cause'),
            'type'=>0,
            'status'=>2
        ]);
        return $this->response->created();
    }

    //申请恢复团长身份
    // public function applyRecover(Request $request){
    //     $request->validate([
    //         'cause'=>'required'
    //     ]);
    //     $user=auth('api')->user();
    //     if($user->hasRole('head')){
    //         return $this->response->errorBadRequest('您未失去团长身份。');
    //     }

    //     $headResign=HeadResign::where('user_id',$user->id);
    //     if($headResign->exists()){

    //         if($headResign->first()->status==2){
    //             if($headResign->first()->type==0){
    //                 return $this->response->errorBadRequest('您的离职申请正在处理中请勿重复请求！');
    //             }else{
    //                 return $this->response->errorBadRequest('您的恢复团长身份申请正在处理中请勿重复请求！');
    //             }
    //         }

    //         $headResign->updated([
    //             'cause'=>$request->input('cause'),
    //             'type'=>1,
    //             'status'=>2
    //         ]);
    //         return $this->response->created();
    //     }
    //     return $this->response->errorBadRequest('您未申请离职！');

    // }
    
    //请求状态
    public function applyStatus(){
        $user=auth('api')->user();
        $headResign=HeadResign::where('user_id',$user->id);
        if(!$headResign->exists()){
            return $this->response->array([
                'status'=>'未申请',
                'type'=>'error'
            ]);
        }else{
            $headResign=$headResign->first();
            return $this->response->array([
                'cause'=>$headResign->cause,
                'status'=>$headResign->status,
                'type'=>$headResign->type
            ]);
        }
    }


    /**
     * 团长工资
     */
    //是否填写收款方式
    public function check_payment_term(){
        $user=auth('api')->user();
        $head=HeadInfo::where('user_id',$user->id)->first();
        $salary=Salary::where('head_id',$head->id)->first();
        if($salary->payment_method==''||$salary->payment_account==''){
            return $this->response->array([
                'status'=>0
            ]);
        }
        return $this->response->array([
            'status'=>1
        ]);
    }
    //填写收款方式
    public function fill_payment_term(Request $request){
        $request->validate([
            'payment_method'=>'required',
            'payment_account'=>'required'
        ]);
        $user=auth('api')->user();
        $head=HeadInfo::where('user_id',$user->id)->first();
        $salary=Salary::where('head_id',$head->id);
        if($salary->first()->payment_method!=''&&$salary->first()->payment_account!=''){
            return $this->response->errorBadRequest('您已填写收款方式！');
        }
        $salary->update([
            'payment_method'=>$request->input('payment_method'),
            'payment_account'=>$request->input('payment_account')
        ]);
        return $this->response->created();

    }
    //查看当月预计收益
    public function forecast_earnings(){
        $user=auth('api')->user();
        $head=HeadInfo::where('user_id',$user->id)->first();
        $packageDeliver=$head->packageDeliver()->first();
        $order_amount=Order::select('amount')
            ->where('package_deliver_id',$packageDeliver->id)
            ->whereIn('status',[5,6])
            ->whereBetween ('signfor_time',[date('Y-m',time()),date('Y-m-d H:i:s',time())])
            ->sum('amount');
        return $this->response->array([
            'amount'=>round($order_amount*env('HEAD_EARNINGS_RATIO'),2)
        ]);
        
    }
    //往月收益
    public function past_earnings(){
        $head=auth('api')->user()->headInfo();
        $payRoll=Payroll::where('head_id',$head->first()->id)->latest()->paginate();
        return $this->response->paginator($payRoll,new PayRollTransformer());
    }
    
    
    //团长信息
    public function headInfoMessage(){
        $head=auth('api')->user()->headInfo()->first();
        $package_deliver=PackageDeliver::where('head_id',$head->id)->first();
        $salary=Salary::where('head_id',$head->id)->first();
        return $this->response->array([
            'head_id'=>$head->id,
            'pic'=>$package_deliver->pic,
            'pic_url'=>oss_url($package_deliver->pic,'delivery'),
            'applicant'=>$head->name,
            'tel'=>$head->tel,
            'id_number'=>$head->id_number,
            'identity'=>$head->identity,
            'store_name'=>$package_deliver->store_name,
            'address'=>$package_deliver->address_id,
            'address_name'=>get_region_name($package_deliver->address_id),
            'store_address'=>$package_deliver->address_info,
            'payment_method'=>$salary->payment_method,
            'payment_account'=>$salary->payment_account,
        ]);
    }

    //申请修改团长信息
    public function applyModifyHeadInfo(Request $request){
        $request->validate([
            'pic'=>'required',
            'applicant'=>'required',
            'tel'=>'required',
            'id_number'=>'required',
            'identity'=>'required',
            'store_name'=>'required',
            'address'=>'required',
            'store_address'=>'required',
            'payment_method'=>'required',
            'payment_account'=>'required'
        ]);
        $head=auth('api')->user()->headInfo()->first();
        $apply=HeadApplyModify::where('head_id',$head->id);
        if($apply->exists()){
            foreach($apply->get() as $val){
                if($val->status==2){
                    return $this->response->errorBadRequest('您的请求还未处理，请耐心等待！');
                }
            }
        }
        HeadApplyModify::create([
            'head_id'=>$head->id,
            'pic'=>$request->input('pic'),
            'applicant'=>$request->input('applicant'),
            'tel'=>$request->input('tel'),
            'id_number'=>$request->input('id_number'),
            'identity'=>$request->input('identity'),
            'store_name'=>$request->input('store_name'),
            'address'=>$request->input('address'),
            'store_address'=>$request->input('store_address'),
            'payment_method'=>$request->input('payment_method'),
            'payment_account'=>$request->input('payment_account'),
            'status'=>2,
        ]);
        return $this->response->created();
    }

}
