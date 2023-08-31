<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use CustomHelper;
use Illuminate\Support\Facades\Hash;

class NotificationSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_setting';

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
        'user_id', 'meta_key', 'meta_value', 'created_at', 'updated_at', 'deleted_at'
    ];

    public static function saveSetting($params)
    {
        $created_at = Carbon::now();
        foreach($params['notification_setting'] as $key => $value){
            $data[] = [
                'user_id'    => $params['user']->id,
                'meta_key'   => $key,
                'meta_value' => $value,
                'created_at' => $created_at
            ];
        }
        self::insert($data);

        return self::getSetting($params['user']->id);
    }

    public static function getSetting($user_id)
    {
        $records = self::where('user_id',$user_id)->get();
        if( count($records) ){
            foreach($records as $record){
                $data[$record->meta_key] = $record->meta_value;
            }
        } else {
            //default notification setting
            $data['post'] = 1;
            $data['comment'] = 1;
        }
        return $data;
    }
}
