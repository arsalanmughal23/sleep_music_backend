<?php

namespace App\Models;

use App\Traits\QueryCacheable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer id
 * @property string name
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 * @property mixed image
 * @property mixed media
 * @property mixed media_all
 * @property mixed category_id
 * @property mixed category
 * @property mixed parent
 * @property mixed parent_id
 * @property bool has_child
 *
 * @SWG\Definition(
 *      definition="Playlist",
 *      required={"user_id", "name", "type", "is_featured", "is_protected"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="image",
 *          description="image",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_featured",
 *          description="is_featured",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="is_protected",
 *          description="is_protected",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="sort_key",
 *          description="sort_key",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Playlist extends Model
{
    use SoftDeletes, QueryCacheable;

    public $table = 'playlists';


    protected $dates = ['deleted_at'];

    // Cache time in Seconds
//    public $cacheFor = 3600;

    public $fillable = [
        'user_id',
        'name',
        'image',
        'type',
        'category_id',
        'parent_id',
        'has_child',
        'is_featured',
        'is_protected'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name'         => 'string',
        'image'        => 'string',
        'category_id'  => 'integer',
        'parent_id'    => 'integer',
        'has_child'    => 'boolean',
        'is_featured'  => 'boolean',
        'is_protected' => 'boolean'
    ];

    protected $attributes = [
        'is_featured'  => false,
        'is_protected' => false
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
//        "image_url",
        "type_text",
        "media",
        "media_count"
    ];

    /**
     * The attributes that should be visible in toArray.
     *
     * @var array
     */
    protected $visible = [
        "id",
        "name",
        "user_id",
        "image",
        "type",
        "category_id",
        "parent_id",
        "has_child",
        "is_featured",
        "is_protected",
        "sort_key",
        "created_at",
        "updated_at",
        "image_url",
        "type_text",
//        "media",
//        "category",
        "media_count"
    ];

    /**
     * Validation create rules
     *
     * @var array
     */
    public static $rules = [
        'user_id'     => 'required',
        'name'        => 'required',
        'image'       => 'required',
        'type'        => 'required',
//        'category_id' => 'required',
//        'parent_id' => 'required',
//        'has_child' => 'required',
        'is_featured' => 'required',
//        'is_protected' => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'user_id'     => 'required',
        'name'        => 'required',
        'image'       => 'sometimes',
        'type'        => 'required',
//        'category_id' => 'required',
//        'parent_id' => 'required',
//        'has_child' => 'required',
        'is_featured' => 'required',
//        'is_protected' => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
//        'user_id'      => 'required',
        'name'  => 'required',
        'image' => 'required',
        'type'  => 'required|in:' . Category::TYPE_AUDIO . "," . Category::TYPE_VIDEO,
//        'category_id'  => 'required',
//        'parent_id'  => 'required',
//        'has_child'  => 'required',
//        'is_featured'  => 'required',
//        'is_protected' => 'required'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
//        'user_id'      => 'required',
        'name'  => 'sometimes',
        'image' => 'sometimes',
        'type'  => 'sometimes',
//        'category_id'  => 'required',
//        'parent_id'  => 'required',
//        'has_child'  => 'required',
//        'is_featured'  => 'required',
//        'is_protected' => 'required'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*public function media()
    {
        return $this->belongsToMany(Media::class, "playlist_media");
    }*/

    public function media_all()
    {
        return $this->belongsToMany(Media::class, "playlist_media");
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parent()
    {
        return $this->belongsTo(Playlist::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Playlist::class, 'parent_id');
    }

    /**
     * @return string
     */
//    public function getImageUrlAttribute()
//    {
//        if (substr($this->image, 0, 4) === "http") {
//            return $this->image;
//        }
////        return $this->image ? \Storage::url($this->image) : route('api.resize', ['img' => 'users/user.png', 'w=100', 'h=100']);
//        $image = "public/playlist.png";
//        if ($this->image) {
//            $image = $this->image;
//        }
//        return route('api.resize', ['img' => $image]);
////        return ($this->image && storage_path('storage/app/' . $this->image)) ? route('api.resize', ['img' => $this->image]) : route('api.resize', ['img' => 'users/user.png', 'w=100', 'h=100']);
////        return \Storage::url($this->image);
//        return route('api.resize', ['img' => $this->image]);
//    }

    /**
     * @return string
     */
    public function getTypeTextAttribute()
    {
        return Category::$TYPES[$this->type];
    }

    public function getMediaCountAttribute()
    {
        return $this->media_all()->count();
    }

    public function getMediaAttribute()
    {
        return $this->media_all->take(10);
    }

    public function getFullNameAttribute()
    {
        return ($this->parent) ? $this->parent->full_name . " > " . $this->name : $this->name;
    }
}
