<?php

namespace App\Models\Hooks\Api;

use App\Helpers\CustomHelper;
use App\Models\UserApiToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\UserInvite;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRegistration;
use App\Mail\BusinessRegistration;

class UserHook
{
    private $_model,
            $except_update_params = [
                'username',
                'slug',
                'email',
                'password',
                'status',
                'is_email_verify',
                'is_mobile_verify',
                'mobile_otp',
                'email_otp',
                'account_approved',
                'remember_token',
            ];

    public function __construct($model)
    {
        $this->_model = $model;
    }

    /*
   | ----------------------------------------------------------------------
   | Hook for manipulate query of index result
   | ----------------------------------------------------------------------
   | @query   = current sql query
   | @request = laravel http request class
   |
   */
    public function hook_query_index(&$query,$request, $slug=NULL) {
        //Your code here
        $query->with(['businessCategory','promoteCategory'])->select('users.*');
        //check same user
        if( $request['user']->slug == $slug )
        {
            $query->with('userPackage');
            $query->selectRaw('api_token,device_type,device_token,platform_type,platform_id')
                ->join('user_api_token AS uat','uat.user_id','=','users.id')
                ->where('uat.api_token',$request['api_token']);
        }
        if( !empty($request['user_group_id']) ){
            $query->where('user_group_id',$request['user_group_id']);
        }
        $query->where('users.user_type','user');
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before add data is execute
    | ----------------------------------------------------------------------
    | @arr
    |
    */
    public function hook_before_add($request,&$postdata)
    {
        //delete un-verified account
        if( env('VERIFICATION_TYPE') != 'none'){
            if( env('VERIFICATION_TYPE') == 'email'){
                $this->_model::where('email',$postdata['email'])->forceDelete();
            }
            if( env('VERIFICATION_TYPE') == 'mobile'){
                $this->_model::where('mobile_no',$postdata['mobile_no'])->forceDelete();
            }
        }
        if( env('MAIL_SANDBOX') ){
            $postdata['is_email_verify'] = '1';
            $postdata['email_verify_at'] = Carbon::now();
        }
        if( !empty($postdata['image_url']) ){
            $postdata['image_url'] = CustomHelper::uploadMedia('users',$postdata['image_url']);
        }
        if( !empty($postdata['id_card']) ){
            $postdata['id_card'] = CustomHelper::uploadMedia('users',$postdata['id_card']);
        }
        if( $postdata['user_group_id'] == 3 ){
            $postdata['account_approved'] = 1;
        }

        $checkUserCount          = $this->_model::count();
        $postdata['referral_id'] = $checkUserCount . Str::random(4) . rand(111,999);
        $postdata['username']    = $this->_model::generateUniqueUserName($postdata['name']);
        $postdata['slug']        = $postdata['username'];
        $postdata['password']    = Hash::make($postdata['password']);
        $postdata['created_at']  = Carbon::now();
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after add public static function called
    | ----------------------------------------------------------------------
    | @record
    |
    */
    public function hook_after_add($request,$record)
    {
        $api_token  = UserApiToken::generateApiToken($record->id,$request->ip(),$request->header('token'),$record->created_at);
        $request['api_token'] = $api_token;
        $request['user']      = $record;
        //insert api token
        \DB::table('user_api_token')
            ->insert([
                'user_id'       => $record->id,
                'api_token'     => $api_token,
                'refresh_token' => NULL,
                'udid'          => $request->header('token'),
                'device_type'   => $request['device_type'],
                'device_token'  => $request['device_token'],
                'platform_type' => !empty($request['platform_type']) ? $request['platform_type'] : 'custom',
                'platform_id'   => !empty($request['platform_id']) ? $request['platform_id'] : NULL,
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->server('HTTP_USER_AGENT'),
                'created_at'    => Carbon::now()
            ]);
        //send verification email
        if( env('VERIFICATION_TYPE') == 'email' && env('MAIL_SANDBOX') == 0 ){

            if( $request['user_group_id'] == 3 ){
                $mail_params['USERNAME'] = $record->name;
                $mail_params['LINK']     = route('verifyEmail',['name' => encrypt($record->email)]);
                $mail_params['YEAR']     = date('Y');
                $mail_params['APP_NAME'] = env('APP_NAME');
                //send mail
                Mail::to($record->email)->send(new UserRegistration($mail_params,'Welcome to '.env('APP_NAME')));
                //sendMail($record->email,'user_registration',$mail_params);
            } else {
                $mail_params['USERNAME'] = $record->name;
                $mail_params['LINK']     = route('verifyEmail',['name' => encrypt($record->email)]);
                $mail_params['YEAR']     = date('Y');
                $mail_params['APP_NAME'] = env('APP_NAME');
                //send mail
                Mail::to($record->email)->send(new BusinessRegistration($mail_params,'Welcome to '.env('APP_NAME')));
                //sendMail($record->email,'business_registration',$mail_params);
            }


        }
        //check referral code
        if( !empty($request['referral_code']) ){
            $this->_model::referralReward($record->id,$request['referral_code']);
        }
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for manipulate data input before update data is execute
    | ----------------------------------------------------------------------
    | @request  = http request object
    | @postdata = input post data
    | @id       = current id
    |
    */
    public function hook_before_edit($request, $slug, &$postData)
    {
        foreach( $postData as $key => $value ){
            if( in_array($key,$this->except_update_params) )
                unset($postData[$key]);
        }
        if( !empty($postData['image_url']) ){
            $postData['image_url'] = CustomHelper::uploadMedia('users',$postData['image_url']);
            //$blur_image = CustomHelper::getBlurHashImage(Storage::path($postData['image_url']));
            $postData['blur_image'] = '';
        }
        if( !empty($postData['banner_image_url']) ){
            $postData['banner_image_url'] = CustomHelper::uploadMedia('users',$postData['banner_image_url']);
        }
        if( !empty($postData['id_card']) ){
            $postData['id_card'] = CustomHelper::uploadMedia('users',$postData['id_card']);
        }
        $postData['updated_at'] = Carbon::now();
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after edit public static function called
    | ----------------------------------------------------------------------
    | @request  = Http request object
    | @$slug    = $slug
    |
    */
    public function hook_after_edit($request, $slug) {
        //Your code here
    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command before delete public static function called
    | ----------------------------------------------------------------------
    | @request  = Http request object
    | @$id      = record id = int / array
    |
    */
    public function hook_before_delete($request, $slug) {
        //Your code here

    }

    /*
    | ----------------------------------------------------------------------
    | Hook for execute command after delete public static function called
    | ----------------------------------------------------------------------
    | @$request       = Http request object
    | @records        = deleted records
    |
    */
    public function hook_after_delete($request,$records) {
        //Your code here

    }

    public function create_cache_signature($request)
    {
        $cache_params = $request->isMethod('post') ? [] : $request->except(['user','api_token']);
        return 'users_api_' . md5(json_encode($cache_params));
    }
}
