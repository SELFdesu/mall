<?php

namespace App\Listeners;

use App\Events\sendSms;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Redis;
use Overtrue\EasySms\EasySms;

class SendSmsListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  sendSms  $event
     * @return void
     */
    public function handle(sendSms $event)
    {
        $code=rand(100000,999999);
        $config=config('sms');
        $time=900;
        Redis::setex($event->type.'smsCode'.$event->phone, $time, $code);

        try{
            $easySms = new EasySms($config);

            $easySms->send($event->phone, [
                'template' => $config['template'],
                'data' => [
                    'code' => $code
                ],
            ]);
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
}
