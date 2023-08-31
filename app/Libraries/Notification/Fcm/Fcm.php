<?php

namespace App\Libraries\Notification\Fcm;

class Fcm
{
    private $_device_token, $_device_type, $_title, $_message, $_badge, $_custom_data;
    /**
     * Fcm constructor.
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
         if( $this->_device_type == 'android' ){
             return $this->_sendPushToAndroid();
         } elseif ( $this->_device_type == 'web'){
             return $this->_sendPushToWeb();
         } else {
             return $this->_sendPushToIos();
         }
    }

    /**
     * This function is used to send push notification to android device
     */
    private function _sendPushToAndroid()
    {
        $notification_data = [
            'registration_ids' => $this->_device_token,
            'notification' => [
                'title'    => $this->_title,
                'body'     => $this->_message,
                'sound'    => 'default',
                'badge'    => $this->_badge,
                'priority' => 'high',
            ],
            'data' => [
                'message'   => [
                    'title' => $this->_title,
                    'body'  => $this->_message,
                    'sound' => 'default',
                ],
                'user_badge'  => $this->_badge,
                'custom_data' => $this->_custom_data,
                'priority'    => 'high',
            ]
        ];
        $this->sendCurl($notification_data);
        return true;
    }

    /**
     * This function is used to send notification to web browser
     */
    private function _sendPushToWeb()
    {
        $notification_data = [
            'registration_ids' => $this->_device_token,
            'data' => [
                'message'   => [
                    'title' => $this->_title,
                    'body'  => $this->_message,
                    'sound' => 'default',
                ],
                'user_badge'  => $this->_badge,
                'custom_data' => $this->_custom_data,
                'priority'    => 'high',
            ]
        ];
        $this->sendCurl($notification_data);
        return true;
    }

    /**
     * This function is used to send notification to ios device
     */
    private function _sendPushToIos()
    {
        $notification_data = [
            'registration_ids' => $this->_device_token,
            'notification' => [
                'title'        => $this->_title,
                'text'         => $this->_message,
                'body'         => $this->_message,
                'sound'        => 'default',
                'badge'        => $this->_badge,
                'custom_data'  => $this->_custom_data,
                'user_badge'   => $this->_badge
            ],
            'priority' => 'high'
        ];
        $this->sendCurl($notification_data);
        return true;
    }

    protected function sendCurl($notification_data)
    {
        $headers = [
            'Authorization: key=' . env('NOTIFICATION_KEY'),
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,env('NOTIFICATION_URL'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification_data));
        $result = curl_exec($ch);
        if ($result === FALSE)
        {
            file_put_contents(base_path('error_notification.txt'),$result);
        }
        curl_close($ch);
        return true;
    }
}