<?php

namespace App\Libraries\Notification;

class Notification
{
    /**
     * @param array $device_tokens
     * @param string $device_type
     * @param string $title
     * @param string $message
     * @param int $badge
     * @param array $custom_data
     */
    public static function sendPush($device_tokens, $device_type, $title, $message, $badge = 0, $custom_data = [])
    {
        $notification_driver    = env('NOTIFICATION_DRIVER');
        $notification_file_path = '\App\Libraries\Notification\\' . $notification_driver . '\\' . $notification_driver;
        $notification = new $notification_file_path($device_tokens, $device_type, $title, $message, $badge, $custom_data);
        $notification->sendPushNotification();
    }
}