<?php

namespace App\Libraries\Sms\Twilio;

use Twilio\Rest\Client;

class Twilio
{
    private $_twilio,$_response;

    public function __construct()
    {
        $sid           = env('TWILIO_SID');
        $token         = env('TWILIO_AUTH_TOKEN');
        $this->_twilio = new Client($sid, $token);

        $this->_response = [
            'code'    => 200,
            'message' => 'success',
            'data'    => []
        ];
    }

    public function sendVerificationCode($mobile_no)
    {
        try{
            $mobile_no    = str_replace("-","",$mobile_no);
            $verification = $this->_twilio->verify->v2->services(env('TWILIO_SERVICE_ID'))
                ->verifications->create("$mobile_no", "sms");
            $this->_response['data'] = $verification;
        } catch (\Exception $e){
            $this->_response['code']    = 400;
            $this->_response['message'] = $e->getMessage();
        }
        return $this->_response;
    }

    public function checkVerification($code, $mobile_no)
    {
        try{
            $mobile_no    = str_replace("-","",$mobile_no);
            $verification_check = $this->_twilio->verify->v2->services(env('TWILIO_SERVICE_ID'))
                ->verificationChecks->create($code, ["to" => $mobile_no]);
            $this->_response['data'] = $verification_check;
        }catch (\Exception $e){
            $this->_response['code']    = 400;
            $this->_response['message'] = $e->getMessage();
        }
        return $this->_response;
    }
}
