<?php

namespace App\Libraries\Notification\OneSignal;

class OneSignal
{
    private $_device_token, $_device_type, $_title, $_message, $_badge, $_custom_data;

    /**
     * One Signal constructor.
     * @param array $device_tokens
     * @param string $device_type  (android | ios)
     * @param string $message
     * @param int $badge
     * @param array $custom_data
     */
    public function __construct($device_tokens, $device_type, $title, $message, $badge = 0, $custom_data = [])
    {
        $this->_device_token = $device_tokens;
        $this->_message      = $message;
        $this->_device_type  = $device_type;
        $this->_title        = $title;
        $this->_badge        = $badge;
        $this->_custom_data  = $custom_data;
    }

    public function sendPushNotification()
    {
        $fields =[
            'app_id'   => env('NOTIFICATION_APP_ID'),
            'include_player_ids' => $this->_device_token,
            'channel_for_external_user_ids' => 'push',
            'data'     => [
                'title'       => $this->_title,
                'message'     => $this->_message,
                'badge'       => $this->_badge,
                'custom_data' => $this->_custom_data
            ],
            'headings' => [ app()->getLocale() => $this->_title ],
            'contents' => [ app()->getLocale() => $this->_message ],
            'isIos'          => $this->_device_type == 'ios' ? true : false,
            'ios_badgeType'  => "Increase",
            'ios_badgeCount' => $this->_badge
        ];
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('NOTIFICATION_URL'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('NOTIFICATION_KEY') ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}

