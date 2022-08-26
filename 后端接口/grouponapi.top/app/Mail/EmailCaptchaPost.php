<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailCaptchaPost extends Mailable
{
    use Queueable, SerializesModels;

    protected $code;
    protected $behavior;
    protected $time;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code,$behavior,$time)
    {
        $this->code=$code;
        $this->behavior=$behavior;
        $this->time=$time;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.SendCaptchaPost',[
            'code'=>$this->code,
            'behavior'=>$this->behavior,
            'time'=>$this->time
        ])->subject($this->behavior);
    }
}
