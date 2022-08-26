<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ArrivalNotice extends Mailable
{
    use Queueable, SerializesModels;

    protected $package_deliver;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($package_deliver)
    {
        //
        $this->package_deliver=$package_deliver;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ArrivalNotice',[
            'package_deliver'=>$this->package_deliver
        ])->subject('到货通知');
    }
}
