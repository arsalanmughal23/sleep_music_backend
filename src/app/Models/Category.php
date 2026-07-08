<?php

namespace App\Models;

use App\Helper\Util;
use App\Traits\QueryCacheable;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer id
 * @property string name
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 * @property mixed image
 * @property mixed playlists_all
 * @property mixed media_all
 *
 * @SWG\Definition(
 *      definition="Category",
 *      required={"parent_id", "name", "type"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="parent_id",
 *          description="parent_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Category extends Model
{
    use SoftDeletes, QueryCacheable, CascadeSoftDeletes;

    public $table = 'categories';

    const TYPE_AUDIO = 10;
    const TYPE_VIDEO = 20;

    public static $TYPES = [
        self::TYPE_AUDIO => 'Audio',
        self::TYPE_VIDEO => 'Video',
    ];

    protected $dates = ['deleted_at'];

    protected $cascadeDeletes = ['categories'];

    // Cache time in Seconds
//    public $cacheFor = 3600;

    public $fillable = [
        'is_premium',
        'name',
        'image',
        'type',
        'position'
        // 'parent_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'        => 'integer',
        'is_premium'=> 'integer',
        'name'      => 'string',
        'type'      => 'integer',
        'position'  => 'integer',
        // 'parent_id' => 'integer',
        // 'type_text' => 'string',
        // 'created_at',
        // 'updated_at',
    ];

    /**
     * The objects that should be append to toArray.
     *
     * @var array
     */
    protected $with = [];

    /**
     * The attributes that should be append to toArray.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
        'media_count',
        // 'type_text',
        // 'media',
        // 'playlists',
    ];

    /**
     * The attributes that should be visible in toArray.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'is_premium',
        'is_mixer',
        'is_unlockable',
        'created_at',
        'updated_at',
        'image',
        'image_url',
        'media_count',
        'position',
        'media_all',
        // 'parent_id',
        // 'type',
        // 'type_text',
        // 'media',
        // 'playlists',
    ];

    /**
     * Validation create rules
     *
     * @var array
     */
    public static $rules = [
        // 'position'  => 'required',
        // 'ximage'     => 'sometimes|mimes:jpg,png,jpeg',
        'parent_id' => 'required',
        'name'      => 'required|unique:categories,name,NULL,id,deleted_at,NULL',
        'type'      => 'required',
        'is_premium'=> 'required|in:0,1',
        'image'     => 'sometimes'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        // 'position'  => 'required',
        // 'ximage'     => 'sometimes|mimes:jpg,png,jpeg',
        'parent_id' => 'required',
        'name'      => 'required|unique:categories,name,NULL,id,deleted_at,NULL',
        'type'      => 'required',
        'is_premium'=> 'required|in:0,1',
        'image'     => 'sometimes'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        // 'position'  => 'required',
        'parent_id' => 'required',
        'name'      => 'required',
        'type'      => 'required'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        // 'position'  => 'required',
        'parent_id' => 'required',
        'name'      => 'required',
        'type'      => 'required'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function categories()
    {
        return $this->hasMany(View::class);
    }

    public function media_all()
    {
        return $this->hasMany(Media::class);
    }

    public function playlists_all()
    {
        return $this->hasMany(Playlist::class);
    }

    public function getTypeTextAttribute()
    {
        return isset(self::$TYPES[$this->type]) ? self::$TYPES[$this->type] : "N/A";
    }

    public function getTitleAttribute()
    {
        return $this->name . ' - ' . $this->type_text;
    }

    /**
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return $this->image ?? url('/public/placeholder-image.png');
    }

    public function getMediaCountAttribute()
    {
        return $this->media_all()->count();
    }

    public function getMediaAttribute()
    {
        return $this->media_all->take(10);
    }

    public function getPlaylistsAttribute()
    {
        return $this->playlists_all->take(10);
    }
}
