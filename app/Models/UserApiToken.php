<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;

class UserApiToken extends Model
{
    use SoftDeletes,CRUDGenerator;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_api_token';

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
        'user_id', 'api_token', 'refresh_token', 'udid', 'device_type', 'device_token',
        'platform_type', 'platform_id', 'ip_address', 'user_agent', 'created_at', 'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'secret_key'
    ];

    /**
     * It is used to enable or disable DB cache record
     * @var bool
     */
    protected $__is_cache_record = false;

    /**
     * @var
     */
    protected $__cache_signature;

    /**
     * @var string
     */
    protected $__cache_expire_time = 1; //days

    public static function generateApiToken($user_id,$ip_address,$udid,$datetime)
    {
        $key = config('constants.JWT_SECRET');
        $issuedAt   = new \DateTimeImmutable();
        $expire     = $issuedAt->modify(config('constants.JWT_EXPIRE_AT'))->getTimestamp();
        $payload = array(
            "iat"  => $issuedAt->getTimestamp(),
            "iss"  => config('constants.JWT_SECRET'),
            "nbf"  => $issuedAt->getTimestamp(),
            "exp"  => $expire,
            "user" => [
                'user_id' => $user_id,
                'ip_address' => $ip_address,
                'udid' => $udid,
                'datetime' => $datetime
            ]
        );
        $token = JWT::encode($payload, $key, 'HS256');
        return $token;
        // $secret_key = config('constants.SECRET_KEY');
        // $token = "$user_id|$udid|$datetime";
        // $token = hash_hmac('sha256', $token, $secret_key);
        // return $token;
    }

    public static function generateRefreshToken($user_id)
    {
        $secret_key = config('constants.SECRET_KEY');
        $token = hash_hmac('sha256', "$user_id|" . Str::random(50) , $secret_key);
        return $token;
    }
}
