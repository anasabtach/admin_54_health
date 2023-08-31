<?php

namespace App\Models;

use App\Helpers\CustomHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CmsUser extends Authenticatable
{
    use CRUDGenerator, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_users';

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
        'cms_role_id', 'user_ref_id', 'name', 'username', 'slug', 'email', 'mobile_no', 'password',
        'image_url', 'status', 'remember_token', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cmsRole()
    {
        return $this->belongsTo(CmsRole::class,'cms_role_id','id');
    }

    public static function getCmsUserByID($id)
    {
        $query = self::with('cmsRole')->where('id',$id)->first();
        return $query;
    }

    public static function auth($email,$password, $remember_me = false)
    {
        if( Auth::guard('cms_user')->attempt(['email' => $email, 'password' => $password , 'status' => 1],$remember_me) ) {
            return true;
        }
        else {
            return false;
        }
    }

    public static function getUserByEmail($email)
    {
        $query = self::where('email',$email)->where('status','1')->first();
        return $query;
    }

    public static function updatePassword($id,$password)
    {
        self::where('id',$id)->update([
            'password' => Hash::make($password)
        ]);
        return true;
    }

    public static function updateProfile($params)
    {
        if( !empty($params['image_url']) )
            $profile_image = CustomHelper::uploadMedia('cms_user',$params['image_url']);
        else
            $profile_image = $params['old_file'];

        self::where('id',CustomHelper::currentUser()->id)
            ->update([
                'name'      => $params['name'],
                'email'     => $params['email'],
                'mobile_no' => $params['mobile_no'],
                'image_url' => $profile_image
            ]);
    }

    public static function generateUniqueUserName($username)
    {
        $username = Str::slug($username, '-');

        $query = self::where('username',$username)->count();
        if( $query > 0){
            $username = $username . $query . rand(111,999);
        }
        return Str::slug($username,'-');
    }
}
