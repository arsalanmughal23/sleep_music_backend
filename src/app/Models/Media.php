<?php

namespace App\Models;

use App\Helper\Util;
use App\Scopes\MediaBeforeTodayScope;
use App\Traits\QueryCacheable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

/**
 * @property integer id
 * @property string name
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 * @property mixed playlist
 * @property mixed file_url
 * @property mixed image
 * @property mixed file_path
 * @property mixed media_length
 * @property mixed media_convert_job
 * @property string drm_ios
 * @property string drm_android
 * @property mixed is_featured
 * @property mixed image_url
 *
 * @SWG\Definition(
 *      definition="Media",
 *      required={"category_id", "name", "is_featured", "image"},
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
 *          property="category_id",
 *          description="category_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="is_featured",
 *          description="is_featured",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="image",
 *          description="image",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="file_path",
 *          description="file_path",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="file_type",
 *          description="file_type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="file_mime",
 *          description="file_mime",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="file_url",
 *          description="file_url",
 *          type="string"
 *      )
 * )
 */
class Media extends Model
{
    use SoftDeletes, QueryCacheable;

    const SUFFIX_ANDROID = "/android/";
    const SUFFIX_IOS     = "/ios/";

    public $table = 'media';


    protected $dates = ['deleted_at'];

    // Cache time in Seconds
//    public $cacheFor = 3600;

    public $fillable = [
        'is_premium',
        'user_id',
        'category_id',
        'name',
        'image',
        'file_url',
        'is_mixer',
        'is_unlockable',
        'duration',

        // 'file_path',
        // 'file_type',
        // 'file_mime',
        // 'playlist',
        // 'media_length',
        // 'public_media',
        // 'is_featured'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'user_id'     => 'integer',
        'category_id' => 'integer',
        'name'        => 'string',
        'image'       => 'string',
        'file_url'    => 'string',
        'duration'    => 'integer',

        // 'file_path'   => 'string',
        // 'file_type'   => 'string',
        // 'file_mime'   => 'string',
        // 'media_length' => 'integer',
        // 'is_featured'  => 'integer',
        // 'public_media' => 'integer',
    ];

    /**
     * The objects that should be append to toArray.
     *
     * @var array
     */
    protected $with = [
        'user',
        'category',
        'mixers'
    ];

    /**
     * The attributes that should be append to toArray.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
        // 'file_absolute_url',
        // 'drm_android_url',
        // 'drm_ios_url',
        // 'original_playlist',
        // 'comments_count',
        // 'likes_count',
        // 'is_liked',
        // 'total_count',
        // 'share_url'

    ];

    /**
     * The attributes that should be visible in toArray.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'duration',
        'is_premium',
        'user_id',
        'category_id',
        'image',
        'image_url',
        'file_url',
        'is_mixer',
        'is_unlockable',
        'mixers',
        // 'user',
        // 'category',
        'created_at',
        'updated_at',
        
        // 'file_path',
        // 'file_type',
        // 'file_mime',
        // 'file_absolute_url',
        // 'is_featured',
        // 'media_length',
        // 'drm_android',
        // 'drm_android_url',
        // 'drm_ios',
        // 'drm_ios_url',
        // 'original_playlist',
        // 'public_media',

        // 'views',
        // 'comments_count',
        // 'likes_count',
        // 'is_liked',
        // 'total_count',
        // 'share_url'
    ];

    /**
     * Validation create rules
     *
     * @var array
     */
    public static $rules = [
        // 'user_id'      => 'required|exists:users,id',
        // 'media_length' => 'required',
        // 'public_media' => 'required',
        'is_premium'  => 'required|in:1,0',
        'category_id' => 'required|exists:categories,id',
        'name'        => 'required',
        'image'       => 'sometimes',
        'file'        => 'sometimes'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        // 'is_premium'  => 'required|in:1,0',
        // 'category_id' => 'required|exists:categories,id',
        // 'name'        => 'required',
        // 'image'       => 'sometimes',
        // 'file'        => 'sometimes',
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        // 'public_media' => 'required',
        'category_id' => 'required',
        'name'        => 'required',
        'image'       => 'required',
        'file_url'    => 'required',
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        // 'public_media' => 'required',
        'category_id' => 'required',
        'name'        => 'required',
        'image'       => 'required',
        'file_url'    => 'sometimes',
    ];

    protected static function boot()
    {
        parent::boot();
        if (intval(config('constants.server_type', Util::SERVER_TYPE_MASTER)) != Util::SERVER_TYPE_MASTER) {
            static::addGlobalScope(new MediaBeforeTodayScope);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function playlist()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_media');
    }

    /**
     * @return string
     */
    public function getImageUrlAttribute()
    {
//        if (substr($this->image, 0, 4) === "http") {
//            return $this->image;
//        }
////        return $this->image ? \Storage::url($this->image) : route('api.resize', ['img' => 'users/user.png', 'w=100', 'h=100']);
//        $image = "public/media.png";
//        if ($this->image) {
//            $image = $this->image;
//        }
//        return route('api.resize', ['img' => $image]);
        return $this->image ? $this->image : url('/public/placeholder-image.png');
//        return ($this->image && storage_path(url('storage/app/' . $this->image))) ? route('api.resize', ['img' => $this->image]) : route('api.resize', ['img' => 'users/user.png', 'w=100', 'h=100']);
//        return \Storage::url($this->image);
//        return route('api.resize', ['img' => $this->image]);
    }

    public function getFileAbsoluteUrlAttribute()
    {
        return starts_with($this->file_url, "http") ? $this->file_url :
//            asset($this->file_url)
            \Storage::url($this->file_path);
    }

    public function getDrmAndroidUrlAttribute()
    {
        return \Storage::url($this->drm_android);
    }

    public function getDrmIosUrlAttribute()
    {
        return \Storage::url($this->drm_ios);
    }

    public function getOriginalPlaylistAttribute()
    {
        return (count($this->playlist) > 0) ? $this->playlist[0]->id : null;
    }

    public function likes()
    {
        return $this->belongsTo(Like::class, 'id', 'media_id')->orderBy('id', 'desc');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function userMedia()
    {
        return $this->hasMany(UserMedia::class, 'media_id');
    }

    public function mixers(){
        return $this->hasMany(Mixer::class, 'media_id');
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getIsLikedAttribute()
    {


//        dd(\Auth::check());
        if (\Auth::check()) {

            $exists = $this->likes()->where('user_id', \Auth::id())->first();
            if ($exists) {

                return 1;
            }
        }
        return 0;
    }

    public function Mediaview()
    {
        return $this->belongsTo(Mediaview::class, 'id', 'media_id');
    }


    public function getTotalCountAttribute()
    {
        $count = $this->Mediaview()->count();

        return $count;

    }

    public function getShareUrlAttribute()
    {
        $url = URL::to('/') . '/get-url/' . $this->id;


        return $url;

    }


}
