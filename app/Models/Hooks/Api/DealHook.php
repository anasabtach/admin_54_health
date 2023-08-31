<?php

namespace App\Models\Hooks\Api;

use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DealHook
{
    private $_model;

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
        $query->with('user')
            ->select('deals.*')
            ->selectRaw('IF(f.id IS NOT NULL,1,0) AS is_favourite')
            ->leftJoin('favourites AS f',function($leftJoin) use ($request){
                $leftJoin->on('f.module_id','=','deals.id')
                        ->where('f.module','deals')
                        ->where('f.user_id',$request['user']->id);
            });
        if( $slug == null ){
            if( !empty($request['user_id']) ){
                $query->where('deals.user_id',$request['user']->id);
            }
            if( !empty($request['paid_promotion']) ){
                $query->whereIn('deals.paid_promotion',$request['paid_promotion']);
            }
            if( isset($request['status']) ){
               $query->where('deals.status',$request['status']);
            }
            if( !empty($request['is_favourite']) ){
                $query->having('is_favourite',1);
            }
            $query->orderBy('deals.paid_promotion','asc');
            $query->orderBy('deals.id','desc');
        }
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
        $postdata['user_id'] = $request['user']->id;
        $postdata['slug'] = $request['user']->id . uniqid();
        $postdata['image_url'] = CustomHelper::uploadMedia('deal',$postdata['image_url']);
        $blur_image = CustomHelper::getBlurHashImage(Storage::path($postdata['image_url']));
        $postdata['blur_image'] = $blur_image;
        $postdata['created_at'] = Carbon::now();
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
        //add user promotion transaction
        if( !empty($request['card_token']) ){
            \DB::table('user_deal_transaction')
                ->insert([
                    'user_id' => $request['user']->id,
                    'deal_id' => $record->id,
                    'gateway_transaction_id' => $request['gateway_transaction_id'],
                    'gateway'       => 'stripe',
                    'charge_amount' => $request['charge_amount'],
                    'expiry_date'   => $request['paid_pormotion_expire_date'],
                    'device_type'   => 'web',
                    'ip_address'    => $request->ip(),
                    'created_at'    => Carbon::now()
                ]);
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
        if( !empty($postData['image_url']) ){
            $postData['image_url'] = CustomHelper::uploadMedia('deal',$postData['image_url']);
            $blur_image = CustomHelper::getBlurHashImage(Storage::path($postData['image_url']));
            $postData['blur_image'] = $blur_image;
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
        $cache_params = $request->except(['user','api_token']);
        return 'DealHook' . md5(implode('',$cache_params));
    }
}
