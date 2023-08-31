<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPackage extends Model
{
    use SoftDeletes,CRUDGenerator;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_packages';

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
        'gateway_transaction_id','gateway_original_transaction_id','gateway','user_id',
        'package_id','user_invite_id','charge_amount', 'expiry_date', 'trial_period','device_type',
        'ip_address','created_at', 'updated_at', 'deleted_at'
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

    public function package()
    {
        return $this->belongsTo(Package::class,'package_id','id');
    }

    public static function getActiveSubscription($user_id)
    {
        $current_date = date('Y-m-d');

        $query = self::where('user_id',$user_id)->where('expiry_date','>',$current_date)
                ->orderBy('id','desc')
                ->first();
        return $query;
    }

    public static function getUserPackageHistory($user_id)
    {
        $query = self::with('package')
                    ->where('user_id',$user_id)
                    ->orderBy('id','desc')
                    ->paginate(config('constants.PAGINATION_LIMIT'));
        return $query;
    }
}
