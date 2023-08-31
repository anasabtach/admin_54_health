<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use SoftDeletes,CRUDGenerator;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_subscriptions';

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
        'user_id', 'type', 'gateway', 'gateway_transaction_id', 'gateway_original_transaction_id', 'subscription_expiry_date',
        'is_trial', 'status', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

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

    public static function getUserActiveSubscription($user_id)
    {
        $query = self::where('user_id',$user_id)->where('status','active')->first();
        return $query;
    }

    public static function expiredUserSubscription($user_id)
    {
        self::where('user_id',$user_id)->update(['status' => 'expired']);
        return true;
    }

    public static function createRewardSubscription($user_id,$subscription_expiry_date)
    {
        $record = self::insert([
            'user_id' => $user_id,
            'type'    => 'rewarded',
            'gateway' => 'invite_reward',
            'gateway_transaction_id' => 0,
            'gateway_original_transaction_id' => 0,
            'subscription_expiry_date' => $subscription_expiry_date,
            'is_trial'   => '0',
            'status'     => 'active',
            'created_at' => Carbon::now(),
        ]);
        return $record;
    }
}
