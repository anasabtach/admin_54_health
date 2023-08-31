<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BusinessRegistration extends Mailable
{
    use Queueable, SerializesModels;

    public $data, $mail_subject, $attachment_path;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data,$mail_subject,$attachment_path=[])
    {
        $this->data            = $data;
        $this->mail_subject    = $mail_subject;
        $this->attachment_path = $attachment_path;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
                     ->subject($this->mail_subject)
                     ->view('email.business_registration')->with($this->data);
        if( !empty($this->attachment_path) ){
            foreach( $this->attachment_path as $attach ){
                $mail->attach($attach);
            }
        }
        return $mail;
    }
}
