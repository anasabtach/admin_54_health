<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserGroup extends Model
{
    use SoftDeletes,CRUDGenerator;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_groups';

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
        'title', 'slug', 'description', 'is_super_admin', 'status', 'created_at', 'updated_at',
        'deleted_at'
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
    protected $__is_cache_record = true;

    /**
     * @var
     */
    protected $__cache_signature;

    /**
     * @var string
     */
    protected $__cache_expire_time = 1; //days

    public static function createRole($params)
    {
        $record = self::create([
            'title'          => $params['name'],
            'slug'           => Str::slug($params['name']),
            'is_super_admin' => $params['is_super_admin'],
            'type'           => $params['type'],
            'created_at'     => Carbon::now()
        ]);
        //check super admin
        if( empty( $params['is_super_admin']) ){
            //module permission
            if( count($params['module_id']) ){
                for( $i=0; $i < count($params['module_id']); $i++ ){
                    $module_permission[] =[
                        'user_id'       => currentUser()->id,
                        'user_group_id' => $record->id,
                        'cms_module_id' => $params['module_id'][$i],
                        'is_add'        => !empty($params['is_add'][$params['module_id'][$i]]) ? '1' : '0',
                        'is_view'       => !empty($params['is_view'][$params['module_id'][$i]]) ? '1' : '0',
                        'is_update'     => !empty($params['is_update'][$params['module_id'][$i]]) ? '1' : '0',
                        'is_delete'     => !empty($params['is_delete'][$params['module_id'][$i]]) ? '1' : '0',
                        'created_at'    => Carbon::now()
                    ];
                }
                CmsModulePermission::insert($module_permission);
            }
        }
        return $record;
    }

    public static function getRecordBySlug($slug)
    {
        $record = self::where('slug',$slug)->first();
        return $record;
    }

    public static function updateRole($params,$slug)
    {
        self::where('slug',$slug)
        ->update([
            'title'          => $params['name'],
            'slug'           => Str::slug($params['name']),
            'is_super_admin' => $params['is_super_admin'],
            'updated_at'     => Carbon::now()
        ]);
        $record = self::getRecordBySlug($slug);
        //delete old module permission
        self::deleteModulePermissionByRoleId($record->id);
        //check super admin
        if( empty( $params['is_super_admin']) ){
            //module permission
            if( count($params['module_id']) ){
                for( $i=0; $i < count($params['module_id']); $i++ ){
                    $module_permission[] =[
                        'user_id'       => currentUser()->id,
                        'user_group_id' => $record->id,
                        'cms_module_id' => $params['module_id'][$i],
                        'is_add'        => !empty($params['is_add'][$params['module_id'][$i]]) ? '1' : '0',
                        'is_view'       => !empty($params['is_view'][$params['module_id'][$i]]) ? '1' : '0',
                        'is_update'     => !empty($params['is_update'][$params['module_id'][$i]]) ? '1' : '0',
                        'is_delete'     => !empty($params['is_delete'][$params['module_id'][$i]]) ? '1' : '0',
                        'created_at'    => Carbon::now()
                    ];
                }
                CmsModulePermission::insert($module_permission);
            }
        }
        return $record;
    }

    public static function deleteModulePermissionByRoleId($role_id)
    {
        if( !is_array($role_id) )
            CmsModulePermission::where('user_group_id',$role_id)->forceDelete();
        else
            CmsModulePermission::whereIn('user_group_id',$role_id)->forceDelete();
    }

    public static function dataTableRecords($params = [])
    {
        $query = self::select('*');
        if(!empty($params['keyword'])){
            $name  = $params['keyword'];
            $query = $query->where('name','like',"$name%");
        }
        $query->where('slug','!=','super-admin')->where('type','admin');
        $data['total_record'] = count($query->get());
        $query = $query->take($params['length'])->skip($params['start'])->orderBy('id','desc');
        $query = $query->get();
        $data['records'] = $query;
        return $data;
    }

    public static function deleteRecord($record_id)
    {
        if( !is_array($record_id) ){
            self::where('slug',$record_id)->delete();
            self::deleteModulePermissionByRoleId($record_id);
        } else {
            self::whereIn('slug',$record_id)->delete();
            self::deleteModulePermissionByRoleId($record_id);
        }
        return true;
    }

    public static function getCmsRole($is_super_role = false)
    {
        $query = \DB::table('user_groups');
        if( $is_super_role == false )
            $query->where('slug','!=','super-admin');

        $query = $query->where('type','admin')
                       ->whereNull('deleted_at')
                       ->orderBy('title','asc')
                       ->get();
        return $query;
    }
}
