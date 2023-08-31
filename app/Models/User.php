<?php

namespace App\Models;

use App\Helpers\CustomHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;
use Stripe\Subscription;
class User extends Authenticatable
{
    use HasFactory, CRUDGenerator, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

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
        'user_group_id','user_type','name','business_name','business_category_id','promote_category_id', 'username','slug',
        'work_email','email','mobile_no','password','image_url','banner_image_url','blur_image','id_card','profession',
        'status','country', 'city', 'state','zipcode','address','latitude','longitude',
        'open_time','close_time','about','product_service','site_url','is_email_verify','email_verify_at','is_mobile_verify',
        'mobile_verify_at', 'account_approved', 'online_status','mobile_otp','email_otp','remember_token',
        'notification_setting','invite_code','subscription_expiry_date','is_web_based_business',
        'business_hours','referral_id','member_type','organization_name','created_at', 'updated_at', 'deleted_at','working_days'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token'
    ];

    /**
     * It is used to enable or disable DB cache record
     * @var bool
     */
    protected $__is_cache_record = true;

    /**
     * @var
     */
    protected $__cache_signature;

    /**
     * @var string
     */
    protected $__cache_expire_time = 1; //days

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class,'user_group_id','id');
    }

    public function userApiToken()
    {
        return $this->hasMany(UserApiToken::class,'user_id','id');
    }

    public function businessCategory()
    {
        return $this->belongsTo(Category::class,'business_category_id','id');
    }

    public function promoteCategory()
    {
        $storage_url = Storage::url('/');
        return $this->belongsTo(Category::class,'promote_category_id','id')
                    ->select('*')
                    ->selectRaw("IF(image_url IS NOT NULL,CONCAT('$storage_url',image_url), NULL) AS image_url");
    }

    public function userPackage()
    {
        return $this->hasOne(UserPackage::class,'user_id','id')
                    ->orderBy('id','desc');
    }

    /**
     * Authentication
     * @param {int} $user_group_id
     * @param {string} $email
     * @param {string} password
     * @param {bool} $remember_me
     * @return {bool}
     */
    public static function auth($user_type, $email,$password, $remember_me = false)
    {
        $credentials = [
            'user_type' => $user_type,
            'email'     => $email,
            'password'  => $password ,
            'status'    => 1
        ];
        if( Auth::attempt($credentials,$remember_me) ) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Get Auth User
     * @param {string} @guard | optional
     */
    public static function getAuthUser($guard = 'web')
    {
        if( Auth::check() ){
            $user = Auth::guard($guard)->user();
            $user->user_group = $user->userGroup;
            return $user;
        } else {
            return [];
        }

    }

    /**
     * Update Admin Profile
     * @param {Array} $params
     * @return {bool}
     */
    public static function updateProfile($params)
    {
        if( !empty($params['image_url']) )
            $profile_image = uploadMedia('users',$params['image_url']);
        else
            $profile_image = $params['old_file'];

        self::where('id',currentUser()->id)
            ->update([
                'name'      => $params['name'],
                'email'     => $params['email'],
                'mobile_no' => $params['mobile_no'],
                'image_url' => $profile_image
            ]);
        return true;
    }

    /**
     * Update Admin Password
     * @param {int} $id
     * @param {string} $password
     * @return {bool}
     */
    public static function updatePassword($id,$password)
    {
        self::where('id',$id)->update([
            'password' => Hash::make($password)
        ]);
        return true;
    }

    /**
     * This function is used to generate unique username
     * @param string $username
     * @return string $username
     */
    public static function generateUniqueUserName($username)
    {
        $username = Str::slug($username);
        $query = self::where('username',$username)->count();
        if( $query > 0){
            $username = $username . $query . rand(111,999);
        }
        return Str::slug($username);
    }

    /**
     * This function is used to get user by email
     * @param $email
     * @return object $query
     */
    public static function getUserByEmail($email)
    {
        $query = self::where('email',$email)->first();
        return $query;
    }

    /**
     * This function is used to update user device token
     * @param illuminate\http\request $request
     * @param object $user
     * @return bool
     */
    public static function updateDeviceToken($request,$user, $platform_type = 'custom')
    {
        $api_token  = UserApiToken::generateApiToken($user->id,$request->ip(),$request->header('token'),$user->created_at);
        $record = UserApiToken::updateOrCreate(
            ['api_token' => $api_token],
            [
                'user_id'       => $user->id,
                'api_token'     => $api_token,
                'refresh_token' => UserApiToken::generateRefreshToken($user->id),
                'udid'          => $request->header('token'),
                'device_type'   => $request['device_type'],
                'device_token'  => $request['device_token'],
                'platform_type' => $platform_type,
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->server('HTTP_USER_AGENT'),
                'created_at'    => Carbon::now(),
            ]
        );
        //new device login attempt
        if( !$record->wasChanged() ){

        }
        return $api_token;
    }

    /**
     * @param $email
     * @param string $module
     * @return false|object
     */
    public static function ForgotPassword($email, $module = 'users')
    {
        $user = self::getUserByEmail($email);
        if( !isset($user->id) )
            return false;
        elseif( $user->status != 1)
            return false;

        $reset_pass_token = Str::random(150);
        ResetPassword::insert([
            'email'      => $email,
            'token'      => $reset_pass_token,
            'created_at' => Carbon::now(),
        ]);
        if( $user->user_group_id == 4 ){
            $reset_url = env('BUSINESS_PANEL_URL') . '/reset-password/' . $reset_pass_token;
        }else{
            $reset_url = env('USER_PANEL_URL') . '/reset-password/' . $reset_pass_token;
        }
        //send reset password email
        $mail_params['USERNAME'] = $user->name;
        $mail_params['LINK']     = $reset_url;
        $mail_params['YEAR']     = date('Y');
        $mail_params['APP_NAME'] = env('APP_NAME');
        sendMail($user->email,'forgot-password',$mail_params);

        return $user;
    }

    public static function updateUser($user_id,$data)
    {
        self::where('id',$user_id)->update($data);
    }

    public static function updateUserByEmail($email,$data)
    {
        self::where('email',$email)->update($data);
    }

    public static function getUserByApiToken($api_token)
    {
        $user = self::with(['businessCategory','promoteCategory','userPackage'])
                    ->select('users.*')
                    ->selectRaw('api_token,device_type,device_token,platform_type,platform_id')
                    ->join('user_api_token AS uat','uat.user_id','=','users.id')
                    ->where('uat.api_token',$api_token)
                    ->first();
        return $user;
    }

    public static function userLogout($params)
    {
        UserApiToken::where('api_token',$params['api_token'])->forceDelete();
        return true;
    }

    public static function socialUser($params)
    {
        $image_url  = null;
        $blur_image = null;

        $data = new \stdClass();
        if( empty($params['email']) )
            $user = self::getUserByPlatformID($params['platform_type'],$params['platform_id']);
        else
            $user = self::getUserByEmail($params['email']);
        //upload image by url
        if( !empty($params['image_url']) ){
            $image_content = @file_get_contents($params['image_url']);
            if( !empty($image_content) ){
                $image_url  = CustomHelper::uploadMediaByContent('users',$image_content);
                $blur_image = CustomHelper::getBlurHashImage(Storage::path($image_url));
            }
        }
        //create new user
        if( !isset($user->id) ){
            $created_at    = Carbon::now();
            $temp_password = Str::random(8);
            $username      = self::generateUniqueUserName($params['name']);
            $record_id = self::insertGetId([
                'user_group_id'   => 2,
                'name'            => $params['name'],
                'username'        => $username,
                'slug'            => $username,
                'email'           => !empty($params['email']) ? $params['email'] : null,
                'password'        => Hash::make($temp_password),
                'mobile_no'       => !empty($params['mobile_no']) ? $params['mobile_no'] : null,
                'image_url'       => $image_url,
                'blur_image'      => $blur_image,
                'is_email_verify' => 1,
                'latitude'        => !empty($params['latitude']) ? $params['latitude'] : null,
                'longitude'       => !empty($params['longitude']) ? $params['longitude'] : null,
                'created_at'      => Carbon::now(),
            ]);
            $data->id = $record_id;
            $data->created_at = $created_at;
        } else {
            //update existing user
            $update_data = [];
            if( !empty($params['name']) )
                $update_data['name'] = $params['name'];
            if( !empty($params['image_url']) ){
                $update_data['image_url'] = $image_url;
                $update_data['blur_image'] = $blur_image;
            }
            if( !empty($params['latitude']) && !empty($params['longitude']) )
                $update_data['latitude'] = $params['latitude'];
                $update_data['longitude'] = $params['longitude'];

            $update_data['is_email_verify'] = 1;
            $update_data['updated_at']      = Carbon::now();
            if( !empty($update_data) )
                self::where('id',$user->id)->update($update_data);

            $data->id = $user->id;
            $data->created_at = $user->created_at;
        }
        return $data;
    }

    public static function getUserByPlatformID($platform_type,$platform_id)
    {
        $query = self::select('users.*')
                    ->selectRaw('api_token,device_type,device_token,platform_type,platform_id')
                    ->join('user_api_token AS uat','uat.user_id','=','users.id')
                    ->where('platform_type',$platform_type)
                    ->where('platform_id',$platform_id)
                    ->first();
        return $query;
    }

    public static function getUserApiTokenByID($user_id)
    {
        $query = self::select('users.*','uat.device_type','uat.device_token')
                    ->join('user_api_token AS uat','uat.user_id','=','users.id')
                    ->where('uat.user_id',$user_id)
                    ->get();
        return $query;
    }

    public static function totalUsers()
    {
        $query = \DB::table('users')
                    ->whereIn('user_group_id',[3,4])
                    ->whereNull('deleted_at')
                    ->count();
        return $query;
    }

    public static function getVendors($params)
    {
        $query = self::with(['businessCategory','promoteCategory'])
                    ->select('users.*')
                    ->where('user_group_id',4);

        if( !empty($params['name']) ){
            $name = $params['name'];
            $query->where('name','like',"%$name%");
        }
        if( !empty($params['promote_category']) ){
            $query->whereIn('promote_category_id',$params['promote_category']);
        }
        if( !empty( $params['latitude'] ) && !empty($params['longitude']) ){
            $radius    = !empty($params['radius']) ? $params['radius'] : 50;
            $latitude  = $params['latitude'];
            $longitude = $params['longitude'];
            $query->whereRaw("
                ( 3959 *
                acos(cos(radians({$latitude})) *
                cos(radians(latitude)) *
                cos(radians(longitude) -
                radians({$longitude})) +
                sin(radians({$latitude})) *
                sin(radians(latitude))) ) < {$radius}
            ");
        }
        $limit = !empty($params['limit']) ? $params['limit'] : config('constants.PAGINATION_LIMIT');
        $query = $query->orderBy('id','desc')->paginate($limit);
        return $query;
    }

    public static function getVendor($slug)
    {
        $query = self::with(['businessCategory'])
                    ->select('users.*')
                    ->where('user_group_id',4)
                    ->where('slug',$slug)
                    ->first();
        return $query;
    }

    public static function getUserByInviteCode($invite_code)
    {
        $query = self::where('invite_code',$invite_code)->first();
        return $query;
    }

    public static function referralReward($user_id,$referral_code)
    {   
        $current_date = date('Y-m-d');
        $invite_user = self::where('referral_id',$referral_code)->first();
        if( isset($invite_user->id) ){
            $record = UserInvite::create([
                'user_id' => $user_id,
                'invite_user_id' => $invite_user->id,
                'status' => 'consumed',
                'created_at' => Carbon::now()
            ]);
            // if( empty($invite_user->subscription_expiry_date) || strtotime($current_date) > strtotime($invite_user->subscription_expiry_date)  ){
            //     $subscription_expiry_date = Carbon::now()->addMonth()->format('Y-m-d');
            // } else {
            //     $subscription_expiry_date = Carbon::parse($invite_user->subscription_expiry_date)->addMonth()->format('Y-m-d');
            // }
            // //insert reward data
            // UserPackage::insert([
            //     'gateway_transaction_id'          => 0,
            //     'gateway_original_transaction_id' => 0,
            //     'gateway'        => NULL,
            //     'user_id'        => $invite_user->id,
            //     'package_id'     => 0,
            //     'user_invite_id' => $record->id,
            //     'charge_amount'  => 0,
            //     'expiry_date'    => $subscription_expiry_date,
            //     'trial_period'   => '0',
            //     'device_type'    => !empty($request['device_type']) ? $request['device_type'] : 'web',
            //     'ip_address'     =>  \Request::ip(),
            //     'created_at'     => Carbon::now()
            // ]);
            // //update user table
            // self::where('id',$invite_user->id)
            //     ->update([
            //         'subscription_expiry_date' => $subscription_expiry_date
            //     ]);
            $user_package = UserPackage::where('user_id', $invite_user->id)->latest()->first();
            
            if(!empty($user_package)){
                $expiry_date = Carbon::parse($user_package->expiry_date)->addMonthsNoOverflow(1)->toDateString();
                Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                $subscriptionId = $user_package->gateway_transaction_id; // Replace with the actual subscription ID
                $subscription = Subscription::retrieve($subscriptionId);
                // Calculate the new billing cycle anchor (timestamp) for the extended subscription
                $daysToAdd = 30; // Number of days to extend the subscription by
                $newBillingCycleAnchor = strtotime("+{$daysToAdd} days");
                $subscription->trial_end = $newBillingCycleAnchor;
                $subscription->save();
                
                //update user package
                $user_package->expiry_date =$expiry_date;
                $user_package->save();

                self::where('id',$invite_user->id)//update user table
                ->update([
                    'subscription_expiry_date' => $expiry_date
                ]);
            }
        }
    }
}
