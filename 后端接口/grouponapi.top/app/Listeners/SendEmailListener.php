<?php

namespace App\Listeners;

use App\Events\SendEmail;
use App\Mail\EmailCaptchaPost;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class SendEmailListener
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
     * @param  SendEmail  $event
     * @return void
     */
    public function handle(SendEmail $event)
    {
        switch($event->type){
            case 1:
                $name='bind_email_';
                $behavior='邮箱绑定';
                break;
            case 2:
                $name='resetpassword_email_';
                $behavior='密码重置';
                break;
            case 3:
                $name='register_email_';
                $behavior='账号注册';
                break;
            default:
                return 0;
        }
        $time=$event->time*60;
        $code=rand(100000,999999);
        Redis::setex($name.$event->email, $time, $code);
        // cache([$name.$email => $code], now()->addMinutes($time));
        Mail::to($event->email)->queue(new EmailCaptchaPost($code,$behavior,$event->time));
    }
}
