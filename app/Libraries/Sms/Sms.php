<?php

namespace App\Libraries\Sms;

class Sms
{
    public function getInstance()
    {
        $sms_driver = env('SMS_DRIVER');
        $gateway_file_path = '\App\Libraries\Sms\\' . $sms_driver . '\\' . $sms_driver;
        return new $gateway_file_path;
    }
}
