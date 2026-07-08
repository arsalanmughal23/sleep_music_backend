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
 *      definition="Comment",
 *      required={"comment", "user_id", "media_id", "parent_id"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="media_id",
 *          description="media_id",
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
 *      ),
 *      @SWG\Property(
 *          property="parent_id",
 *          description="parent_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class Comment extends Model
{
    use SoftDeletes;

    public $table = 'comments';


    /*
LIKE = 1
FOLLOW = 2
COMMENT = 3
*/
    const COMMENT = 30;
    protected $dates = ['deleted_at'];


    public $fillable = [
        'comment',
        'user_id',
        'media_id',
        'parent_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'       => 'integer',
        'comment'  => 'string',
        'user_id'  => 'integer',
        'media_id' => 'integer',

    ];

    /**
     * The objects that should be append to toArray.
     *
     * @var array
     */
    protected $with = [
        'user',
        'children'
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
        'comment'  => 'required',
        'user_id'  => 'required',
        'media_id' => 'required',

    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'comment'  => 'required',
        'user_id'  => 'required',
        'media_id' => 'required',

    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        'comment'  => 'required',
        //'user_id' => 'required',
        'media_id' => 'required',

    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
//        'comment'  => 'required',
//        'user_id'  => 'required',
//        'media_id' => 'required',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function children()
    {
//        return $this->hasMany(Comment::class, 'parent_id', 'id')->take(2);
        return $this->hasMany(Comment::class, 'parent_id', 'id');

    }

    public function gethumanDateAttribute()
    {
//        $dateTime = \Carbon\Carbon::createFromTimestamp(strtotime('created_at'));
//        return $dateTime->diffForHumans('created_at');
        return $this->created_at->diffForHumans();
    }

}
