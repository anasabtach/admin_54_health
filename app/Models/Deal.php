<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class Deal extends Model
{
    use SoftDeletes,CRUDGenerator;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'deals';

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
        'user_id','name','slug','image_url','blur_image','description','price_type','price',
        'sale_price','discount_percentage','time_bound','start_date','end_date','redeem_type',
        'redeem_length','deal_code','status','paid_promotion','paid_pormotion_expire_date',
        'total_rating','total_review','deal_redeemed_count','deal_redeemed_user_count','marketing_deal_redeemed_count',
        'marketing_deal_redeemed_user_count','created_at', 'updated_at','deleted_at', 'deal_type',
        'how_to_redeem'
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
                    ->select('id','name','slug','address')
                    ->selectRaw("IF(image_url is not null,CONCAT('$storage_url',image_url),CONCAT('$base_url','/images/user-placeholder.jpg')) AS image_url");
    }

    public static function getDealBySlug($slug)
    {
        $query = self::where('slug',$slug)->first();
        return $query;
    }

    public static function totalOngoingDeals($user_id)
    {
        $query = self::where('user_id',$user_id)
                    ->where('time_bound','ongoing')
                    ->where('paid_promotion','0')
                    ->count();
        return $query;
    }

    public static function totalOngoingMarketingDeals($user_id)
    {
        $query = self::where('user_id',$user_id)
                    ->where('time_bound','ongoing')
                    ->where('paid_promotion','1')
                    ->count();
        return $query;
    }

    public static function totalDealRedeemed($user_id)
    {
        $query = self::where('deals.user_id',$user_id)
                    ->selectRaw('DISTINCT deals.id AS total')
                    ->join('deal_redeem','deal_redeem.deal_id','=','deals.id')
                    ->where('deals.time_bound','ongoing')
                    ->where('deals.paid_promotion','0')
                    ->groupBy('deals.id')
                    ->first();

        return empty($query->total) ? 0 : $query->total;
    }

    public static function totalMarketingDealRedeemed($user_id)
    {
        $query = self::where('deals.user_id',$user_id)
                    ->selectRaw('DISTINCT deals.id AS total')
                    ->join('deal_redeem','deal_redeem.deal_id','=','deals.id')
                    ->where('deals.time_bound','ongoing')
                    ->where('deals.paid_promotion','1')
                    ->groupBy('deals.id')
                    ->first();

        return empty($query->total) ? 0 : $query->total;
    }

    public static function totalUserDealRedeemed($user_id)
    {
        $query = self::where('deals.user_id',$user_id)
                    ->join('deal_redeem','deal_redeem.deal_id','=','deals.id')
                    ->where('deals.time_bound','ongoing')
                    ->where('deals.paid_promotion','0')
                    ->groupBy('deals.id')
                    ->count();
        return $query;
    }

    public static function totalUserMarketDealRedeemed($user_id)
    {
        $query = self::where('deals.user_id',$user_id)
                        ->join('deal_redeem','deal_redeem.deal_id','=','deals.id')
                        ->where('deals.time_bound','ongoing')
                        ->where('deals.paid_promotion','1')
                        ->groupBy('deals.id')
                        ->count();
        return $query;
    }

    public static function vendorDeals($user_id,$login_user_id)
    {
        $query = self::with('user')
                    ->select('deals.*')
                    ->selectRaw('IF(f.id IS NOT NULL,1,0) AS is_favourite')
                    ->leftJoin('favourites AS f',function($leftJoin) use ($login_user_id){
                        $leftJoin->on('f.module_id','=','deals.id')
                                ->where('f.module','deals')
                                ->where('f.user_id',$login_user_id);
                    })
                    ->where('deals.user_id',$user_id)
                    ->whereIn('paid_promotion',['1','0'])
                    ->where('status','1')
                    ->orderBy('deals.paid_promotion','asc')
                    ->orderBy('deals.id','desc')
                    ->paginate( config('constants.PAGINATION_LIMIT') );
        return $query;
    }

    public static function vendorDeal($slug,$user_id)
    {
        $query = self::with('user')
                    ->select('deals.*')
                    ->selectRaw('IF(f.id IS NOT NULL,1,0) AS is_favourite,
                    IF(dr.id IS NOT NULL,1,0) AS is_redeem')
                    ->leftJoin('favourites AS f',function($leftJoin) use ($user_id){
                        $leftJoin->on('f.module_id','=','deals.id')
                                ->where('f.module','deals')
                                ->where('f.user_id',$user_id);
                    })
                    ->leftJoin('deal_redeem AS dr', function($leftJoin) use ($user_id){
                        $leftJoin->on('dr.deal_id','=','deals.id')
                                 ->where('dr.user_id','=',$user_id);
                    })
                    ->where('slug',$slug)
                    ->first();
        return $query;
    }

    public static function vendorRelatedDeals($params)
    {
        $query = self::with('user')
                        ->where('user_id',$params['user_id'])
                        ->whereIn('paid_promotion',$params['paid_promotion'])
                        ->orderBy('deals.paid_promotion','asc')
                        ->orderBy('deals.id','desc')
                        ->take($params['limit'])
                        ->get();
        return $query;
    }

    public static function addOrRemoveFavourite($params)
    {
        $checkRecord = \DB::table('favourites')
                            ->where('module','deals')
                            ->where('user_id',$params['user']->id)
                            ->where('module_id',$params['deal_id'])
                            ->first();
        if( isset($checkRecord->id) ){
            \DB::table('favourites')->where('id',$checkRecord->id)->delete();
        } else {
            \DB::table('favourites')
                ->insert([
                    'user_id'    => $params['user']->id,
                    'module'     => 'deals',
                    'module_id'  => $params['deal_id'],
                    'created_at' => Carbon::now(),
                ]);
        }
        //get Deal
        $record = self::with('user')
                    ->select('deals.*')
                    ->selectRaw('IF(f.id IS NOT NULL,1,0) AS is_favourite')
                    ->leftJoin('favourites AS f',function($leftJoin) use ($params){
                        $leftJoin->on('f.module_id','=','deals.id')
                                 ->where('f.module','deals')
                                 ->where('f.user_id',$params['user']->id);
                    })
                    ->where('deals.id',$params['deal_id'])
                    ->first();
        return $record;
    }

    public static function checkDealRedeem($deal_id,$user_id)
    {
        $record = \DB::table('deal_redeem')
                    ->where('deal_id',$deal_id)
                    ->where('user_id',$user_id)
                    ->count();
        return $record;
    }

    public static function dealRedeem($params,$deal_type)
    {
        \DB::table('deal_redeem')
            ->insert([
                'user_id'    => $params['user']->id,
                'deal_id'    => $params['deal_id'],
                'created_at' => Carbon::now(),
            ]);
        //market deal redeem
        $dealRedeemUserCount = self::dealRedeemUserCount($params['deal_id']);
        if($deal_type){
            \DB::table('deals')
                ->where('id',$params['deal_id'])
                ->increment('marketing_deal_redeemed_count',1,['marketing_deal_redeemed_user_count' => $dealRedeemUserCount ]);
        } else {
            \DB::table('deals')
                ->where('id',$params['deal_id'])
                ->increment('deal_redeemed_count',1,['deal_redeemed_user_count' => $dealRedeemUserCount ]);
        }
        return true;
    }

    public static function dealRedeemUserCount($deal_id)
    {
        $query = \DB::table('deal_redeem')
                    ->selectRaw('COUNT(DISTINCT user_id) AS total')
                    ->where('deal_id',$deal_id)
                    ->first();
        return !empty($query->total) ? $query->total : 0;
    }
}
