<?php

namespace App\Libraries\Sms\TeleSign;
use telesign\sdk\messaging\MessagingClient;
use function telesign\sdk\util\randomWithNDigits;

class TeleSign
{
    private $_response;

    public function __construct()
    {
        $this->_response = [
            'code'    => 200,
            'message' => 'success',
            'data'    => []
        ];
    }

    public function sendVerificationCode($mobile_no)
    {
        $mobile_no = preg_replace('/[-+]/s','',$mobile_no);
        $customer_id = env('TELESIGN_CUSTOMER_ID');
        $api_key = env('TELESIGN_API_KEY');
        $verify_code = randomWithNDigits(5);
        $message = "Your code is $verify_code";
        $message_type = "OTP";
        $messaging = new MessagingClient($customer_id, $api_key);
        $response = $messaging->message($mobile_no, $message, $message_type);
        if( $response->status_code == 200 ){
            $response->verification_code = $verify_code;
            $this->_response['data'] = $response;
        } else {
            $this->_response = [
                'code'    => 400,
                'message' => $response->json['status']['description'],
                'data'    => []
            ];
        }
        return $this->_response;
    }

    public function checkVerification($code,$mobile_no)
    {
        $params = \Request::all();
        if( $code != $params['user']->mobile_otp ){
            $this->_response = [
                'code'    => 400,
                'message' => __('app.otp_invalid'),
                'data'    => []
            ];
        }
        return $this->_response;
    }
}
