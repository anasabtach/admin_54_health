<?php

namespace App\Models\Hooks\Admin;

use Illuminate\Support\Facades\Hash;

class UserHook
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
    public function hook_query_index(&$query,$request, $slug=NULL)
    {
        $query->select('users.*','ug.title AS user_group');
        $query->join('user_groups AS ug','ug.id','=','users.user_group_id');

        if( \Route::currentRouteName() == 'cms-users-management.ajax-listing' ){
            $query->where('users.user_group_id','!=',1);
            $query->where('users.user_type','admin');
        }

        if( \Route::currentRouteName() == 'app-users.ajax-listing' ){
            $query->where('users.user_type','user')
                  ->where('user_group_id',3);
        }

        if( \Route::currentRouteName() == 'vendor.ajax-listing' ){
            $query->where('users.user_type','user')
                  ->where('user_group_id',4);
        }

        if( !empty($request['keyword']) ){
            $keyword = $request['keyword'];
            $query->where(function($where) use ($keyword){
                $where->orWhere('users.name','like',"$keyword%");
                $where->orWhere('users.email','like',"$keyword%");
                $where->orWhere('users.mobile_no','like',"$keyword%");
                $where->orWhere('users.created_at','like',"$keyword%");
            });
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
        $postdata['user_type'] = 'admin';
        $postdata['username']  = $this->_model::generateUniqueUserName($postdata['name']);
        $postdata['slug']      = $postdata['username'];
        $postdata['password']  = Hash::make($postdata['password']);
        if( !empty($postdata['image_url']) ){
            $postdata['image_url'] = uploadMedia('users',$postdata['image_url'],'50X50');
        }

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
        //Your code here
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
            $postData['image_url'] = uploadMedia('users',$postData['image_url'],'50X50');
        }
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
        return 'UserAdmin_' . md5(implode('',$cache_params));
    }
}
