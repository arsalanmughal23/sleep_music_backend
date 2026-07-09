<?php

namespace App\Models;

use App\Helper\Util;
use App\Traits\QueryCacheable;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use phpDocumentor\Reflection\Types\Self_;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property integer id
 * @property integer user_id
 * @property string first_name
 * @property string last_name
 * @property string phone
 * @property string address
 * @property string image
 * @property integer area_id
 * @property integer is_verified
 * @property integer email_updates
 * @property integer is_social_login
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 * @property string image_url
 *
 * @property User user
 *
 * @SWG\Definition(
 *      definition="Details",
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="phone",
 *          description="phone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="address",
 *          description="address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="email_updates",
 *          description="email_updates, acceptable values 0,1. ",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="image",
 *          description="image",
 *          type="file"
 *      )
 * )
 */
class UserDetail extends Model
{
    use SoftDeletes, QueryCacheable;

    public $table = 'user_details';

    const GENDER_MALE   = 1;
    const GENDER_FEMALE = 2;
    const GENDER_OTHER  = 3;
    const GENDER_TEXT   = [
        self::GENDER_MALE   => "Male",
        self::GENDER_FEMALE => "Female",
        self::GENDER_OTHER  => "Non Binary"
    ];
    // Cache time in Seconds
//    public $cacheFor = 3600;


    // public static function boot()
    // {
    //     parent::boot();
    //     static::creating(function($model)
    //     {
    //         $model->free_trial_expiry = \Carbon\Carbon::now()->addDays(config('constants.free_trail_days'));
    //         $model->is_free_trial_used = 1;
    //         $model->is_free_trial = 1;
    //         $model->is_subscribed = 0;
    //     });
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'image',
        'email_updates',
        'is_social_login',
        'push_notifications',
        'stripe_customer_id',
        'connect_account_id',
        // 'phone',
        // 'address',
        // 'gender',
        // 'dob',
        // 'username',
        // 'about',

        // 'free_trial_expiry',
        // 'is_free_trial',
        // 'is_subscribed',
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
        'media_views',
        'full_name',
        'gender_text',
        'total_followers',
        'total_following',
        'is_following',
        'is_app_rating'
    ];

    /**
     * The attributes that should be visible in toArray.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'first_name',
        'last_name',
        'image',
        'image_url',
        'is_verified',
        'is_social_login',
        'media_count',
        'is_app_rating',
        'push_notifications',
        'stripe_customer_id',
        'connect_account_id',
        'email_updates',
        'is_free_trial_used',
        'is_subscribed',

        // 'free_trial_expiry',
        // 'is_free_trial',

        // 'phone',
        // 'address',
        // 'area_id',
        // 'media_views',
        // 'gender',
        // 'dob',
        // 'username',
        // 'full_name',
        // 'gender_text',
        // 'total_followers',
        // 'total_following',
        // 'is_following',
        // 'about',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return $this->image ?? url('/public/user.png');
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function getMediaCountAttribute()
    {
        return $this->user->media()->count();
    }

    public function getMediaViewsAttribute()
    {
        return $this->user->media()->sum('views');
    }

    public function getGenderTextAttribute()
    {
        return isset(self::GENDER_TEXT[$this->gender]) ? self::GENDER_TEXT[$this->gender] : "N/A";
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'followed_user_id', 'user_id');
    }

    public function following()
    {
        return $this->hasMany(Follow::class, 'followed_by_user_id', 'user_id');
    }

    public function getTotalFollowersAttribute()
    {
        return $this->followers()->count();
    }

    public function getTotalFollowingAttribute()
    {
        return $this->following()->count();
    }

    public function getIsFollowingAttribute()
    {
        if (\Auth::check()) {
            $exists = Follow::where('followed_user_id', $this->user_id)->where('followed_by_user_id', \Auth::id())->count();
            if ($exists) {
                return 1;
            }
        }

        return 0;
    }

    public function getIsAppRatingAttribute()
    {
        return (boolean)$this->user->appRating()->count();
    }

}
