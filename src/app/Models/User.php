<?php

namespace App\Models;

use App\Traits\QueryCacheable;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Zizaco\Entrust\Traits\EntrustUserTrait;


/**
 * @property integer id
 * @property string name
 * @property string email
 * @property string password
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 * @property string roles_csv
 *
 * @property Role roles
 * @property UserDetail details
 * @property UserDevice devices
 * @property SocialAccount social_accounts
 *
 * @SWG\Definition(
 *      definition="User",
 *      required={"name", "email", "password"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, CascadeSoftDeletes, QueryCacheable;

    use SoftDeletes {
        restore as private restoreA;
    }
    use EntrustUserTrait {
        restore as private restoreB;
    }

    protected $cascadeDeletes = ['details', 'devices', 'socialAccounts', 'media', 'playlists', 'comments', 'like', 'follower', 'following', 'notifications', 'trending', 'trend', 'views', 'userNotification', 'transactions'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The objects that should be append to toArray.
     *
     * @var array
     */
    protected $with = [
        'details',
    ];

    /**
     * The attributes that should be append to toArray.
     *
     * @var array
     */
    protected $appends = [
        'user_active_subscription',
        'under_request_subscription'
    ];

    /**
     * The attributes that should be visible in toArray.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'email',
        'details',
        'created_at',
        'user_active_subscription',
        'under_request_subscription'
    ];

    /**
     * Validation create rules
     *
     * @var array
     */
    public static $rules = [];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [       
        'name'                  => 'sometimes|max:300',
        'first_name'            => 'required|max:180',
        'last_name'             => 'required|max:180',
        'image'                 => 'sometimes'
    ];

    public static $api_delete_account_rules = [
        'current_password' => 'nullable',
        // 'delete_type_name' => 'required|max:180|exists:delete_types,name,deleted_at,NULL',
        // 'delete_reason' => 'max:300|required_if:delete_type_name,other'
    ];

    public function restore()
    {
        $this->restoreA();
        $this->restoreB();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getRolesCsvAttribute()
    {
        return implode(",", $this->roles->pluck('display_name')->all());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function details()
    {
        return $this->hasOne(UserDetail::class, 'user_id', 'id');
    }

    public function getUserActiveSubscriptionAttribute()
    {
        return $this->userAllSubscriptions()
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->orderBy('created_at', 'desc')
                ->first();
    }

    public function getUnderRequestSubscriptionAttribute()
    {
        return $this->userAllSubscriptions()
                ->where('status', UserSubscription::STATUS_HOLD)
                ->orderBy('created_at', 'desc')
                ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function like()
    {
        return $this->hasMany(Like::class);
    }

    public function follower()
    {
        return $this->hasMany(Follow::class, 'followed_user_id');
    }

    public function following()
    {
        return $this->hasMany(Follow::class, 'followed_by_user_id');
    }

    public function notifications()
    {
        return $this->hasMany(NotificationUser::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function trending()
    {
        return $this->hasMany(TrendingArtist::class, 'artist_id');
    }

    public function trend()
    {
        return $this->hasMany(TrendingArtist::class, 'user_id');
    }

    public function views()
    {
        return $this->hasMany(View::class, 'user_id');
    }

    public function appRating()
    {
        return $this->hasOne(AppRating::class);
    }

    public function userMedia()
    {
        return $this->hasMany(UserMedia::class, 'user_id');
    }

    public function userNotification()
    {
        return $this->belongsTo(NotificationUser::class, 'user_id', 'id');
    }

    public function getIsSubscriberAttribute()
    {
        return $this->userAllSubscriptions()->exists();
    }

    public function userAllSubscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'user_id');
    }
}
