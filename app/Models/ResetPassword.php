<?php

namespace App\Models;

use App\Helpers\CustomHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ResetPassword extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reset_password';

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
        'email', 'token', 'created_at', 'updated_at', 'deleted_at'
    ];

    public static function resetPassword($email)
    {
        $record = CmsUser::getUserByEmail($email);
        if( !isset($record->id) )
            return false;

        $token = encrypt($record->email);
        $mail_param['USERNAME'] = $record->name;
        $mail_param['LINK']     = route('admin.reset-password',['any' => $token ]);
        $mail_param['YEAR']     = date('Y');
        $mail_param['APP_NAME'] = appSetting('application_setting','application_name');
        sendMail($record->email,'forgot-password',$mail_param);

        //delete old request
        self::where('email',$record->email)->forceDelete();
        //generate new request
        self::insert([
            'email'      => $record->email,
            'token'      => $token,
            'created_at' => Carbon::now()
        ]);
        return true;
    }

    public static function getRequest($token)
    {
        $token  = decrypt($token);
        $params = explode('|',$token);
        $record = self::where('module',$params[0])->where('email',$params[1])->first();
        return $record;
    }

    public static function updateResetPassword($password,$token)
    {
        $token  = decrypt($token);
        $params = explode('|',$token);
        //delete request
        self::where('module',$params[0])->where('email',$params[1])->forceDelete();
        //save new password
        \DB::table($params[0])->where('email',$params[1])->update([
            'password' => Hash::make($password)
        ]);
        return true;
    }

    public static function getUserRequest($token)
    {
        $query = self::select('reset_password.*')
                    ->selectRaw('u.id AS user_id, u.user_group_id')
                    ->join('users AS u','u.email','=','reset_password.email')
                    ->where('token',$token)
                    ->first();
        return $query;
    }
}
