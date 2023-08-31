<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class CmsModule extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_modules';

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
         'parent_id', 'name', 'route_name', 'icon', 'status', 'sort_order', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function child()
    {
        return $this->hasMany(CmsModule::class,'parent_id','id');
    }

    /**
     * @param int $role_id
     * @return mixed
     */
    public static function getModules($role_id = 0)
    {
        $query = self::select('cms_modules.*','cmp.is_add','cmp.is_view','cmp.is_update','cmp.is_delete')
            ->with([ 'child' => function($query) use ($role_id){
                $query->select('cms_modules.*','cmp.is_add','cmp.is_view','cmp.is_update','cmp.is_delete')
                    ->leftJoin('cms_module_permissions AS cmp',function($leftJoin) use ($role_id){
                        $leftJoin->on('cmp.cms_module_id','=','cms_modules.id')
                            ->where('cmp.user_group_id','=',$role_id);
                    });
                $query->where('status','1')->orderBy('sort_order','asc');
            }
            ])
            ->leftJoin('cms_module_permissions AS cmp',function($leftJoin) use ($role_id){
                $leftJoin->on('cmp.cms_module_id','=','cms_modules.id')
                    ->where('cmp.user_group_id','=',$role_id);
            })
            ->where('parent_id',0)
            ->where('status','1')
            ->orderBy('sort_order','asc')
            ->get();
        return $query;
    }

    /**
     * @param $role_id
     * @return mixed
     */
    public static function getUserModules()
    {
        $modules = [];
        $authUser = currentUser();
        if( !empty($authUser) ){
            $authUser = $authUser->toArray();
            if( $authUser['user_group']['is_super_admin'] )
                $modules = self::getSuperAdminModules();
            else
                $modules = self::getSubAdminModules($authUser['user_group_id']);
        }
        return $modules;
    }

    /**
     * @return mixed
     */
    public static function getSuperAdminModules()
    {
        $query = Cache::rememberForever('super_admin_modules', function () {
            return  self::with(['child' => function($query){
                            $query->where('status',1)->orderBy('sort_order','asc');
                        }])
                        ->where('parent_id',0)
                        ->where('status','1')
                        ->orderBy('sort_order','asc')
                        ->get();
        });
        return $query;
    }

    public static function getSubAdminModules($role_id)
    {
        $data = Cache::rememberForever('module_permission_' . $role_id, function () use ($role_id){
            $main_modules = [];
            $modules      = [];
            $parent_ids   = [];
            $query = \DB::table('cms_module_permissions AS cmp')
                            ->selectRaw('cm.*, cmp.is_add, cmp.is_view,cmp.is_update, cmp.is_delete')
                            ->join('cms_modules AS cm','cm.id','=','cmp.cms_module_id')
                            ->where('user_group_id',$role_id)
                            ->whereRaw('(cmp.is_add = 1 OR cmp.is_view = 1 OR cmp.is_update = 1 OR cmp.is_delete = 1)')
                            ->where('cm.status','1')
                            ->orderBy('cm.sort_order','asc')
                            ->get();
            if( count($query) ){
                foreach($query as $result){
                    $result->child = [];
                    $modules[$result->parent_id][] = $result;
                    if( $result->parent_id != 0)
                        $parent_ids[] = $result->parent_id;
                }
                $main_modules = $modules[0];
                if( count($parent_ids) ){
                    $parent_modules = \DB::table('cms_modules')->whereIn('id',array_unique($parent_ids))->get();
                    foreach($parent_modules as $pm) {
                        $pm->child      = $modules[$pm->id];
                        $main_modules[] = $pm;
                    }
                }
            }
            return $main_modules;
        });
        return $data;
    }

    public static function getCurrentRoutePrivilege()
    {
        $current_url = parse_url(url()->current());
        $current_url = explode('/',$current_url['path']);
        $module_name = $current_url[2];
        $authUser = currentUser()->toArray();
        $query = self::join('cms_module_permissions AS cmp','cmp.cms_module_id','=','cms_modules.id')
                        ->where('cms_modules.route_name','like',"%$module_name%")
                        ->where('cmp.user_group_id',$authUser['user_group']['id'])
                        ->first();
        return $query;
    }
}

