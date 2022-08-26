<?php

namespace App\Console\Commands;

use App\Models\Order;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class OrderExpireListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '监听订单创建，在15分钟后如果没付款取消订单。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cachedb = config('database.redis.default.database',0);
        $pattern = '__keyevent@'.$cachedb.'__:expired';

        Redis::subscribe([$pattern],function ($channel){     // 订阅键过期事件

            $key_type = explode(':',$channel)[0];
            if($key_type=='ORDER_CONFIRM'){
                $order_id=explode(':',$channel)[1];
                $order=Order::find($order_id);
                if($order->status==1){
                    try{
                        DB::beginTransaction();

                        $order->status=99;
                        $order->save();

                        foreach($order->orderDetail as $detail){
                            $detail->product->increment('stock',$detail->quantity);
                        }
        
                        DB::commit();
                    } catch (Exception $e){
                        echo 'err:'.$e;
                        DB::rollBack();
                        Log::error($e);
                    }
                }

            }

        });

    }
}
