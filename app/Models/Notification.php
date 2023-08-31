<?php

namespace App\Models;

use App\Libraries\Notification\Notification as PushNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use CustomHelper;
use Illuminate\Support\Facades\URL;

class Notification extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id', 'identifier', 'actor_id', 'target_id', 'module', 'module_id', 'reference_id', 'reference_module',
        'title', 'description', 'web_redirect_link', 'is_read', 'is_view', 'created_at', 'updated_at',
    ];

    public static function getUserNotifications($params, $unique_id = 0)
    {
        $base_url    = \Storage::url('/');
        $placeholder = URL::to('images/user-placeholder.jpg');
        $limit = !empty($params['limit']) ? $params['limit'] : config('constants.PAGINATION_LIMIT');
        $query = self::select('notifications.*')
                        ->selectRaw("u.name AS actor_name, u.email AS actor_email,
                        IF(u.image_url IS NOT NULL, CONCAT('$base_url',u.image_url), '$placeholder') AS actor_image_url")
                        ->join('users AS u','u.id','=','notifications.actor_id')
                        ->where('notifications.target_id',$params['user']->id)
                        ->orderBy('notifications.id','desc');
        if( !empty($unique_id) )
            $query = $query->where('unique_id',$unique_id)->first();
        else
            $query = $query->paginate($limit);
        //update notification
        Notification::where('notifications.target_id',$params['user']->id)->update([
            'is_read'   => 1
        ]);
        return $query;
    }

    public static function updateNotification($params,$unique_id)
    {
        self::where('unique_id',$unique_id)
            ->where('target_id',$params['user']->id)
            ->update([
                'is_read' => 1,
                'is_view' => 1,
            ]);
        return self::getUserNotifications($params,$unique_id);
    }

    /**
     * @param $identifier
     * @param $data
     * @param array $customData
     * @param bool $bulk_notification
     * @param $device_type
     */
    public static function sendPushNotification($identifier,$notification_data,$customData = [], $device_type = 'android')
    {
        //device token
        $device_token = [];
        foreach($notification_data['target'] as $notifyUser)
        {
            if( !empty($notifyUser->device_token) )
                $device_token[] =  $notifyUser->device_token;
        }

        $unique_id = uniqid();
        $customData['unique_id'] = $unique_id;
        if( !empty($device_token) )
        {

            $pushNotification = new PushNotification;
            $pushNotification->sendPush($device_token,
                $device_type,
                $notification_data['title'],
                $notification_data['message'],
                !empty($notification_data['badge']) ? $notification_data['badge'] : 0,
                $customData);
        }
        $target_users = [];
        foreach( $notification_data['target'] as $target_user ){
            $target_users[$target_user->id] = $target_user;
        }

        foreach($target_users as $notifyUser)
        {
            $notification_buld_data[] = [
                'unique_id'         => $unique_id,
                'identifier'        => $identifier,
                'actor_id'          => $notification_data['actor']->id,
                'target_id'         => $notifyUser->id,
                'module'            => $notification_data['module'],
                'module_id'         => $notification_data['module_id'],
                'reference_id'      => !empty($notification_data['reference_id']) ? $notification_data['reference_id'] : NULL,
                'reference_module'  => !empty($notification_data['reference_module']) ? $notification_data['reference_module'] : NULL,
                'title'             => $notification_data['title'],
                'description'       => $notification_data['message'],
                'web_redirect_link' => !empty($notification_data['redirect_link']) ? $notification_data['redirect_link'] : NULL,
                'created_at'        => Carbon::now()
            ];
        }
        self::insert($notification_buld_data);
        return true;
    }

    public static function getBadge($user_id)
    {
        $query = \DB::table('notifications')
            ->where('target_id',$user_id)
            ->where('is_read',0)
            ->count();
        return $query;
    }
}
