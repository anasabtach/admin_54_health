<?php

namespace App\Libraries\Payment;

class payment
{

    private static $instance = null;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = self::gateway();
        }
        return self::$instance;
    }

    private static function gateway()
    {
        $gateway_type      = env('GATEWAY_TYPE');
        $gateway_file_path = '\App\Libraries\Payment\\' . $gateway_type . '\\' . $gateway_type;
        return new $gateway_file_path;
    }
}
