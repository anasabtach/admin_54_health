<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

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
        'module', 'module_id', 'filename', 'original_name', 'file_url', 'file_url_blur', 'thumbnail_url', 'mime_type',
        'file_type', 'driver', 'media_type', 'meta', 'created_at', 'updated_at', 'deleted_at'
    ];
}
