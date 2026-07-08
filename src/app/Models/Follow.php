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
 *      definition="Follow",
 *      required={"followed_user_id", "followed_by_user_id"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="followed_user_id",
 *          description="followed_user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="followed_by_user_id",
 *          description="followed_by_user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
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
class Follow extends Model
{
    use SoftDeletes;

    /*
LIKE = 1
FOLLOW = 2
COMMENT = 3
*/
    const FOLLOW = 20;

    public $table = 'follows';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'followed_user_id',
        'followed_by_user_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                  => 'integer',
        'followed_user_id'    => 'integer',
        'followed_by_user_id' => 'integer'
    ];

    /**
     * The objects that should be append to toArray.
     *
     * @var array
     */
    protected $with = [
        'user'
    ];

    /**
     * The attributes that should be append to toArray.
     *
     * @var array
     */
    protected $appends = [];

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
        'followed_user_id'    => 'required',
        'followed_by_user_id' => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'followed_user_id'    => 'required',
        'followed_by_user_id' => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        'followed_user_id'    => 'required',
        'followed_by_user_id' => 'sometimes'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        'followed_user_id'    => 'required',
        'followed_by_user_id' => 'required'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'followed_user_id', 'id');
    }

    public function follower()
    {
        return $this->belongsTo(User::class, 'followed_by_user_id', 'id');
    }
}
