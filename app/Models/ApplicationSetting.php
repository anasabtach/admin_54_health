<?php

namespace App\Models;

use App\Helpers\CustomHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ApplicationSetting extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'application_setting';

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
        'identifier', 'meta_key', 'value', 'is_file', 'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getRecords($identifer = '')
    {
        return self::where('identifier',$identifer)->get();
    }

    /**
     * This function is used to save application setting from admin
     * @param array $params
     * @return bool
     */
    public static function saveAppSetting(array $params)
    {
        $data['application_name'] = $params['application_name'];

        if (!empty($params['logo']))
            $data['logo'] = uploadMedia('application_setting', $params['logo']);

        if (!empty($params['favicon']))
            $data['favicon'] = uploadMedia('application_setting', $params['favicon']);

        $checkKey = [];
        foreach ($data as $key => $value) {
            $checkKey[] = $key;
            $app_setting[] = [
                'identifier' => 'application_setting',
                'meta_key' => $key,
                'value' => $value,
                'is_file' => !empty(\Request::hasFile($key)) ? 1 : 0,
                'created_at' => Carbon::now()
            ];
        }

        //delete old app setting
        self::where('identifier', 'application_setting')->whereIn("meta_key", $checkKey)->forceDelete();
        //save new app setting
        self::insert($app_setting);
        //remove application setting from cache
        Cache::forget('setting_application_setting');
        return true;
    }
}
