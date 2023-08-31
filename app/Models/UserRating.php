<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UserRating extends Model
{
    use SoftDeletes,CRUDGenerator;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_rating';

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
        'slug','user_id','module','module_id','rating','review','status','created_at',
        'updated_at', 'deleted_at'
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

    public function user()
    {
        $storage_url = Storage::url('/');
        $base_url    = URL::to('/');
        return $this->belongsTo(User::class,'user_id','id')
                    ->select('id','name','slug')
                    ->selectRaw("IF(image_url is not null,CONCAT('$storage_url',image_url),CONCAT('$base_url','/images/user-placeholder.jpg')) AS image_url");
    }

    public static function checkRecord($user_id,$module,$module_id)
    {
        $query = self::where('user_id',$user_id)
                    ->where('module_id',$module_id)
                    ->where('module',$module)
                    ->first();
        return $query;
    }

    public static function getRatingBySlug($slug)
    {
        $query = self::where('slug',$slug)->first();
        return $query;
    }

    public static function getAvgRating($module,$module_id)
    {
        $query = self::selectRaw('COUNT(id) AS total_review, ROUND(SUM(rating) / COUNT(id),1) AS avg_rating')
                    ->where('module',$module)
                    ->where('module_id',$module_id)
                    ->first();
        return $query;
    }

    public static function getReviews($user_id)
    {
        $query = self::with('user')
                    ->where('module','users')
                    ->where('module_id',$user_id)
                    ->orderBy('id','desc')
                    ->take(50)
                    ->get();
        return $query;
    }
}
