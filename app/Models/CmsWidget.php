<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use CustomHelper;

class CmsWidget extends Model
{
    use SoftDeletes,CRUDGenerator;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_widgets';

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
        'title', 'description', 'icon', 'color', 'div_column_class', 'link', 'widget_type', 'sql', 'config', 'status', 'created_at', 'updated_at', 'deleted_at'
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

    public static function getWidgetByType($widget_type)
    {
        $user  = currentUser()->toArray();
        $query = self::select('cms_widgets.*');
        if( $user['user_group']['is_super_admin'] != 1 ){
            $query->join('cms_widget_role','cms_widget_role.cms_widget_id','=','cms_widgets.id');
            $query->where('cms_widget_role.user_group_id',$user['user_group_id']);
        }
        $query = $query->where('widget_type',$widget_type)->where('status','1')->get();
        return $query;
    }
}
