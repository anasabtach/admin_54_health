<?php

namespace App\Http\Controllers\Api;

use App\Libraries\Sms\Sms;
use App\Models\User;
use App\Models\Deal;
use App\Models\UserApiToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\RestController;
use App\Models\UserRating;
use App\Models\UserInvite;

class UserController extends RestController
{
    public function __construct(Request $request)
    {   
        $this->middleware('custom_auth:api')->only(['index','show','update','changePassword','userLogout',
            'verifyCode', 'resendCode']);
        parent::__construct('User');
        $this->__request     = $request;
        $this->__apiResource = 'Auth';
    }

    /**
     * This function is used to validate restfull routes
     * @param $action
     * @param string $slug
     * @return array
     */
    public function validation($action,$slug=NULL)
    {
        $validator = [];
        $request = $this->__request;

        $custom_messages = [
            'password.regex'     => __('app.password_regex'),
            'latitude.required'  => 'Address is not valid',
            'longitude.required' => 'Address is not valid',
            'id_card.required'   => 'Official ID field is required',
            'mobile_no.regex'    => 'Mobile number format is invalid'
        ];
        switch ($action){
            case 'POST':
                $validator = Validator::make($this->__request->all(), [
                    'user_group_id' => 'required|in:2,3,4',
                    'name'          => ['required','min:3','max:50','regex:/^([A-Za-z0-9\s])+$/'],
                    'business_name' => 'min:3|max:100',
                    'email'         => ['required', 'email',
                        Rule::unique('users')
                    ],
                    'profession'    => 'min:2|max:50',
                    'id_card'       => [
                        Rule::requiredIf(function() use ($request) {
                            return $request['user_group_id'] == 3 ? true : false;
                        }),
                        'image',
                        'max:5120'
                    ],
                    'mobile_no'     => [
                        Rule::unique('users')->whereNull('deleted_at'),
                        'regex:/^(\+?\d{1,3}[-])\d{9,11}$/'
                    ],
                    'password'         => ['required','regex:/^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])(?=.*[a-z]).{8,150}$/'],
                    'confirm_password' => 'required|same:password',
                    'about'            => 'min:3|max:1000',
                    'business_category_id' => 'nullable|numeric',
                    'product_service'      => 'min:3|max:1000',
                    'open_time'            => 'date_format:H:i:s',
                    'close_time'           => 'date_format:H:i:s',
                    'business_hours'       => 'numeric',
                    'address'              => 'min:2|max:200',
                    'state'                => 'min:2|max:200',
                    'city'                 => 'min:2|max:200',
                    'latitude'             => Rule::requiredIf( function() use ($request){
                        return $request['user_group_id'] == 4 ? true : false;
                    }),
                    'longitude'            => Rule::requiredIf( function() use ($request){
                        return $request['user_group_id'] == 4 ? true : false;
                    }),
                    'promote_category_id'  => 'numeric',
                    'site_url'             => 'nullable|url|max:150',
                    'image_url'            => 'image|max:5120',
                    'referral_code'        => 'exists:users,referral_id|max:50',
                ],$custom_messages);
                break;
            case 'PUT':
                $this->__request->merge(['slug' => $slug]);
                $validator = Validator::make($this->__request->all(), [
                    'slug'          => 'exists:users,slug,deleted_at,NULL,id,' . $this->__request['user']->id,
                    'name'          => ['min:3','max:50','regex:/^([A-Za-z0-9\s])+$/'],
                    'business_name' => 'min:3|max:100',
                    'image_url'     => ['sometimes','max:5120'],
                    'banner_image_url' => ['sometimes','max:5120'],
                    'profession'    => 'min:2|max:50',
                    'id_card'       => 'image|max:5120',
                    'business_name' => 'min:2|max:150',
                    'business_category_id' => 'nullable|numeric',
                    'promote_category_id'  => 'numeric',
                    'mobile_no'     => [
                        Rule::unique('users')->ignore($slug,'slug')->whereNull('deleted_at'),
                        'regex:/^(\+?\d{1,3}[-])\d{9,11}$/'
                    ],
                    'about'            => 'min:3|max:1000',
                    'product_service'  => 'min:3|max:1000',
                    'open_time'        => 'date_format:H:i:s',
                    'close_time'       => 'date_format:H:i:s',
                    'business_hours'       => 'numeric',
                    'address'          => 'min:2|max:200',
                    'state'            => 'min:2|max:200',
                    'city'             => 'min:2|max:200',
                    'latitude'             => 'sometimes',
                    'longitude'            => 'sometimes',
                    'device_type'      => 'in:web,android,ios',
                    'promote_category_id'  => 'numeric',
                    'site_url'             => 'nullable|url|max:150',
                ],$custom_messages);
                break;
        }
        return $validator;
    }

    /**
     * GET Request Hook
     * This function is run before a model load
     * @param $request
     */
    public function beforeIndexLoadModel($request)
    {
        $this->__apiResource = 'PublicUser';
    }

    /**
     * @param $request
     * @param $record
     */
    public function afterIndexLoadModel($request,$record)
    {

    }

    /**
     * POST Request Hook
     * This function is run before a model load
     * @param $request
     */
    public function beforeStoreLoadModel($request)
    {
        if( env('VERIFICATION_TYPE') == 'mobile' ) {
            if( env('SMS_SANDBOX',1) != 1){
                $sms = new Sms;
                $response = $sms->getInstance()->sendVerificationCode($request['mobile_no']);
                if( $response['code'] != 200 ){
                    $this->__is_error = true;
                    return $this->__sendError(__('app.validation_msg'),['message' => __('app.invalid_mobile_no') ],400);
                } else {
                    if( env('SMS_DRIVER') == 'TeleSign' ){
                        $request->merge(['mobile_otp' => $response['data']->verification_code ]);
                    }
                }
            }
        }
    }

    /**
     * @param $request
     * @param $record
     */
    public function afterStoreLoadModel($request,$record)
    {
        if( $request['user_group_id'] == 4 ){
            $this->__success_store_message = __('app.vendor_success_store_message');
        }
        if( $request['user_group_id'] == 3 ){
            $this->__success_store_message = __('app.user_account_created');
        }
    }

    /**
     * Get Single Record hook
     * This function is run before a model load
     * @param {object} $request
     * @param {string} $slug
     */
    public function beforeShowLoadModel($request,$slug)
    {
        if( $request['user']->slug != $slug ){
            $this->__apiResource = 'PublicUser';
        }
    }

    /**
     * @param $request
     * @param $record
     */
    public function afterShowLoadModel($request,$record)
    {

    }

    /**
     * Update Request Hook
     * This function is run before a model load
     * @param {object} $request
     * @param {string} $slug
     */
    public function beforeUpdateLoadModel($request,$slug)
    {

    }

    /**
     * @param $request
     * @param $record
     */
    public function afterUpdateLoadModel($request,$record)
    {

    }

    /**
     * Delete Request Hook
     * This function is run before a model load
     * @param {object} $request
     * @param {string} $slug
     */
    public function beforeDestroyLoadModel($request,$slug)
    {

    }

    /**
     * @param $request
     * @param $slug
     */
    public function afterDestroyLoadModel($request,$slug)
    {

    }

    public function verifyCode()
    {
        $request = $this->__request;
        $param_rules['code'] = 'required';
        $response = $this->__validateRequestParams($request->all(),$param_rules);

        if( $this->__is_error )
            return $response;

        if( env('SMS_SANDBOX',1) != 1){
            $sms = new Sms;
            $response = $sms->getInstance()->checkVerification($request['code'],$request['user']->mobile_no);
            if( $response['code'] != 200 ){
                $this->__is_error = true;
                return $this->__sendError(__('app.validation_msg'),['message' => $response['message'] ],400);
            }
        }

        User::updateUser($request['user']->id,
            [ 'is_mobile_verify' => 1 ,
              'mobile_verify_at' => Carbon::now(),
              'mobile_otp' => NULL] );

        //get updated token record
        $user = User::getUserByApiToken($request['api_token']);

        $this->__is_collection = false;
        $this->__is_paginate   = false;

        return $this->__sendResponse($user,200,__('app.otp_verified'));
    }

    public function resendCode()
    {
        $request = $this->__request;
        if( env('SMS_SANDBOX',1) != 1){
            $sms = new Sms;
            $response = $sms->getInstance()->sendVerificationCode($request['user']->mobile_no);
            if( $response['code'] != 200 ){
                $this->__is_error = true;
                return $this->__sendError(__('app.validation_msg'),['message' => __('app.invalid_mobile_no') ],400);
            }else{
                if( env('SMS_DRIVER') == 'TeleSign' ){
                    User::where('id',$request['user']->id)->update(['mobile_otp' => $response['data']->verification_code ]);
                }
            }
        }

        $this->__is_paginate   = false;
        $this->__is_collection = false;

        return $this->__sendResponse($request['user'],200,__('app.resend_otp_msg'));
    }

    public function login()
    {
        $request = $this->__request;
        $param_rule['email']         = 'required|email';
        $param_rule['password']      = 'required';
        $param_rule['device_type']   = 'required|in:android,ios,web';
        $param_rule['device_token']  = 'required';
        $param_rule['user_group_id'] = 'required|in:3,4';

        $response = $this->__validateRequestParams($request->all(),$param_rule);
        if( $this->__is_error )
            return $response;

        $user = User::getUserByEmail($request['email']);
        if( !isset($user->id) )
            return $this->__sendError(__('app.validation_msg'),['message' => __('app.login_failed_msg')] ,400);
        if( $user->user_group_id != $request['user_group_id'] )
            return $this->__sendError(__('app.validation_msg'),['message' => __('app.login_failed_msg')] ,400);
        if( !Hash::check($request['password'],$user->password) )
            return $this->__sendError(__('app.validation_msg'),['message' => __('app.login_failed_msg')] ,400);
        if( $user->status != 1)
            return $this->__sendError(__('app.validation_msg'),['message' => __('app.account_disabled')], 400);
        if( env('VERIFICATION_TYPE') != 'none'){
            if( env('VERIFICATION_TYPE') == 'email' && $user->is_email_verify != 1){
                return $this->__sendError(__('app.validation_msg'),['message' => __('app.email_not_verified')], 400);
            }
            if( env('VERIFICATION_TYPE') == 'mobile' && $user->is_mobile_verify != 1){
                return $this->__sendError(__('app.validation_msg'),['message' => __('app.mobile_not_verified')], 400);
            }
        }
        if( $user->account_approved == '0' ){
            return $this->__sendError(__('app.validation_msg'),['message' => __('app.account_approved_msg')], 400);
        }
        //update device token
        $api_token = User::updateDeviceToken($request,$user);
        //get updated token record
        $user = User::getUserByApiToken($api_token);

        $this->__is_collection = false;
        $this->__is_paginate   = false;
        return $this->__sendResponse($user,200,__('app.login_success_msg'));
    }

    public function forgotPassword()
    {
        $request = $this->__request;
        $param_rule['email'] = 'required|email';
        $param_rule['user_group_id'] = 'required|in:3,4';

        $response = $this->__validateRequestParams($request->all(),$param_rule);
        if( $this->__is_error )
            return $response;

        $record = User::ForgotPassword($request['email']);
        if( $record == false )
            return $this->__sendError(__('app.validation_msg'),['message' => __('app.invalid_email')], 400);

        if( $record->user_group_id != $request['user_group_id'] )
            return $this->__sendError(__('app.validation_msg'),['message' => __('app.invalid_email')], 400);

        $this->__collection  = false;
        $this->__is_paginate = false;
        return $this->__sendResponse([],200,__('app.forgot_password_success_msg'));
    }

    public function changePassword()
    {
        $request = $this->__request;
        $custom_messages = [
            'new_password.regex' => __('app.password_regex')
        ];
        $param_rule['current_password'] = 'required';
        $param_rule['new_password']     = [
            'required',
            'different:current_password',
            'regex:/^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])(?=.*[a-z]).{8,150}$/'
        ];
        $param_rule['confirm_password'] = 'required|same:new_password';

        $response = $this->__validateRequestParams($request->all(),$param_rule,$custom_messages);
        if( $this->__is_error )
            return $response;

        if( !Hash::check($request['current_password'],$request['user']->password) )
            return $this->__sendError(__('app.validation_msg'),['message' => __('app.invalid_old_password') ]);

        //update user new password
        User::updateUser($request['user']->id,['password' => Hash::make($request['new_password'])]);
        //delete api token
        UserApiToken::where('api_token','!=',$request['api_token'])->forceDelete();

        $this->__is_paginate   = false;
        $this->__is_collection = false;

        return $this->__sendResponse($request['user'],200,__('app.password_success_msg'));
    }

    public function socialLogin()
    {
        $request = $this->__request;
        $param_rule['name']          = 'nullable|min:3|max:50';
        $param_rule['email']         = 'nullable|email';
        $param_rule['platform_id']   = 'required|max:255';
        $param_rule['platform_type'] = 'required|in:facebook,google,apple';
        $param_rule['device_type']   = 'required|in:android,ios';
        $param_rule['device_token']  = 'required';
        $param_rule['image_url']     = 'nullable|url';

        $response = $this->__validateRequestParams($request->all(),$param_rule);
        if( $this->__is_error )
            return $response;

        $user = User::socialUser($request->all());

        //update device token
        $api_token = User::updateDeviceToken($request,$user,$request['platform_type']);
        //get updated token record
        $user = User::getUserByApiToken($api_token);

        $this->__is_collection = false;
        $this->__is_paginate   = false;
        return $this->__sendResponse($user,200,__('app.login_success_msg'));
    }

    public function userLogout()
    {
        $request = $this->__request;
        User::userLogout($request->all());

        $this->__collection  = false;
        $this->__is_paginate = false;
        return $this->__sendResponse([],200,__('app.logout_msg'));
    }

    public function getStatistics()
    {
        $request = $this->__request;
        $data['total_users']   = User::totalUsers();
        $data['ongoing_deals'] = Deal::totalOngoingDeals($request['user']->id);
        $data['ongoing_marketing_deals'] = Deal::totalOngoingMarketingDeals($request['user']->id);
        $data['total_deal_redeemed'] = Deal::totalDealRedeemed($request['user']->id);
        $data['total_market_deal_redeemed'] = Deal::totalMarketingDealRedeemed($request['user']->id);
        $data['total_user_deal_redeemed'] = Deal::totalUserDealRedeemed($request['user']->id);
        $data['total_user_merket_deal_redeemed'] = Deal::totalUserMarketDealRedeemed($request['user']->id);

        $this->__is_paginate = false;
        $this->__collection  = false;

        return $this->__sendResponse($data,200,'Statistic retrieved successfully');
    }

    public function vendors()
    {
        $request = $this->__request;
        $records = User::getVendors($request->all());

        $this->__apiResource = 'PublicUser';
        return $this->__sendResponse($records,200,'vendors retrieved successfully');
    }

    public function vendor($slug)
    {
        $request = $this->__request;
        $records = User::getVendor($slug);

        $this->__apiResource   = 'PublicUser';
        $this->__is_paginate   = false;
        $this->__is_collection = false;

        return $this->__sendResponse($records,200,'vendor retrieved successfully');
    }

    public function vendorDeals()
    {
        $request = $this->__request;
        $param_rule['user_id'] = 'required|numeric';

        $response = $this->__validateRequestParams($request->all(),$param_rule);
        if( $this->__is_error )
            return $response;

        $records = Deal::vendorDeals($request['user_id'],$request['login_user_id']);

        $this->__apiResource = 'Deal';
        return $this->__sendResponse($records,200,'vendors retrieved successfully');
    }

    public function vendorDeal($slug)
    {
        $request = $this->__request;
        $record = Deal::vendorDeal($slug,$request['user_id']);
        $user_id = $request['user_id'];
        $rating = UserRating::select('*')
    
        ->leftJoin('users AS U',function($leftJoin) use ($user_id){
                        $leftJoin->on('U.id','=','user_rating.user_id');
                    })->
        
        where('module','deals')->where('module_id',$record->id)->get();
       // dd($rating);
        $record->rating = $rating;
        $this->__apiResource   = 'Deal';
        $this->__is_collection = false;
        $this->__is_paginate   = false;
//dd($record);
        return $this->__sendResponse($record,200,'Deal retrieved successfully');
    }

    public function vendorRelatedDeals()
    {
        $request = $this->__request;
        $param_rule['user_id'] = 'required|numeric';
        $param_rule['paid_promotion'] = 'required';
        $param_rule['limit'] = 'required|numeric';

        $response = $this->__validateRequestParams($request->all(),$param_rule);
        if( $this->__is_error )
            return $response;

        $records = Deal::vendorRelatedDeals($request->all());

        $this->__apiResource = 'Deal';
        $this->__is_paginate = false;

        return $this->__sendResponse($records,200,'Related deals retrieved successfully');
    }

    public function vendorRating()
    {
        $request = $this->__request;
        $param_rule['user_id'] = 'required|numeric';

        $response = $this->__validateRequestParams($request->all(),$param_rule);
        if( $this->__is_error )
            return $response;

        $records = UserRating::getReviews($request['user_id']);

        $this->__apiResource = 'UserRating';
        $this->__is_paginate = false;

        return $this->__sendResponse($records,200,'Review retrieved successfully');
    }

    public function userInvite()
    {
        $request = $this->__request;
        $records = UserInvite::getInviteUsers($request->all());
        return $this->__sendResponse($records,200,'Review retrieved successfully');
    }
}
