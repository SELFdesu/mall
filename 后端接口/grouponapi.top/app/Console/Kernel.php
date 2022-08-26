<?php

namespace App\Console;

use App\Models\HeadInfo;
use App\Models\Order;
use App\Models\PackageDeliver;
use App\Models\Payroll;
use App\Models\Salary;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        //每日检查是否有过期订单未清除
        $schedule->call(function () {
            $orders=Order::where('status',1)
                ->where('created_at','<',date('Y-m-d H:i:s',time()-900))
                ->with('orderDetail.product')
                ->get();
            
            try{
                DB::beginTransaction();


                foreach($orders as $order){
                    $order->status=99;
                    $order->save();

                    foreach($order->orderDetail as $details){
                        $details->product->increment('stock',$details->quantity);
                    }
                }

                DB::commit();
            } catch (Exception $e){
                DB::rollBack();
                Log::error($e);
            }
        })->dailyAt('5:00');

        // 每月对团长工资进行处理
        $schedule->call(function () {
            $packageDeliver=PackageDeliver::select('id','head_id')->where('status',1)->get();
            
            $last= strtotime("-1 month", time());
            $last_lastday = date("Y-m-t", $last);//上个月最后一天
            $last_firstday = date('Y-m-01', $last);//上个月第一天

            // 查找是否有上月未支付报酬的记录存入往月工资表中
            try{
                DB::beginTransaction();
                $untreated_salary=Salary::where('status',0);
                if($untreated_salary->exists()){
                    foreach($untreated_salary->get() as $val){
                        Payroll::create([
                            'head_id'=>$val->head_id,
                            'salary_id'=>$val->id,
                            'salary'=>$val->salary,
                            'payment_method'=>$val->payment_method==''?'该团长未填写收款方式':$val->payment_method,
                            'payment_account'=>$val->payment_account==''?'该团长未填写收款账号':$val->payment_account,
                            'status'=>$val->status,
                            'date'=>date('Y-m', $last)
                        ]);
                    }
                }
                
                // 将上月收益存入salary表中
                foreach($packageDeliver as $val){
                    $oneOrderSalary=Order::select('amount')
                        ->where('package_deliver_id',$val->id)
                        ->whereIn('status',[5,6])
                        ->whereBetween ('signfor_time',[$last_firstday,$last_lastday])
                        ->sum('amount');
                    $oneSalary=Salary::where('head_id',$val->head_id);
                    if($oneSalary->exists()){
                        $oneSalary->update([
                            'salary'=>$oneOrderSalary*env('HEAD_EARNINGS_RATIO'),
                            'status'=>0
                        ]);
                    }

                }
                DB::commit();
            } catch (Exception $e){
                DB::rollBack();
                Log::error($e);
            }

        })->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
