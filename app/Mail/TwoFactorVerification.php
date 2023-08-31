<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
 
class TwoFactorVerification extends Mailable
{
    use Queueable, SerializesModels;
 

    // protected $email             = null;
    protected $verification_code = null;

    public function __construct($verification_code)
    {
        // $this->email             = $email;
        $this->verification_code = $verification_code;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.two_factor_verification', ['verification_code'=>$this->verification_code]);
    }
}