<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer id
 * @property string name
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 * @SWG\Definition(
 *      definition="NotificationUser",
 *      required={"notification_id", "user_id", "status"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="notification_id",
 *          description="notification_id",
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
 *          property="status",
 *          description="status",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="deleted_at",
 *          description="deleted_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class NotificationUser extends Model
{
    use SoftDeletes;

    public $table = 'notification_users';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'notification_id',
        'user_id',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'              => 'integer',
        'notification_id' => 'integer',
        'user_id'         => 'integer',
        'status'          => 'boolean'
    ];

    /**
     * The objects that should be append to toArray.
     *
     * @var array
     */
    protected $with = [
        'notification',
        'user'

    ];

    /**
     * The attributes that should be append to toArray.
     *
     * @var array
     */
    protected $appends = [
        'humanDate'
    ];

    /**
     * The attributes that should be visible in toArray.
     *
     * @var array
     */
    protected $visible = [];

    /**
     * Validation create rules
     *
     * @var array
     */
    public static $rules = [
        'notification_id' => 'required',
        'user_id'         => 'required',
        'status'          => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'notification_id' => 'required',
        'user_id'         => 'required',
        'status'          => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        'notification_id' => 'required',
        'user_id'         => 'required',
        'status'          => 'required'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        'notification_id' => 'required',
        'user_id'         => 'required',
        'status'          => 'required'
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function gethumanDateAttribute()
    {

        return $this->created_at->diffForHumans();
    }
}
