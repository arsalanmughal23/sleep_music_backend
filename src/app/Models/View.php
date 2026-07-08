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
 *      definition="View",
 *      required={"id", "user_id", "media_id", "created_at", "updated_at", "deleted_at"},
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
 *      )
 * )
 */
class View extends Model
{
    use SoftDeletes;

    public $table = 'views';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'id',
        'user_id',
        'media_id',
        'category_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'          => 'integer',
        'user_id'     => 'integer',
        'media_id'    => 'integer',
        'category_id' => 'integer'
    ];

    /**
     * The objects that should be append to toArray.
     *
     * @var array
     */
    protected $with = [
//        'media',
        'categories'
    ];

    /**
     * The attributes that should be append to toArray.
     *
     * @var array
     */
    protected $appends = [

    ];

    /**
     * The attributes that should be visible in toArray.
     *
     * @var array
     */
    protected $visible = [

    ];

    /**
     * Validation create rules
     *
     * @var array
     */
    public static $rules = [
//        'id' => 'required',
//        'user_id' => 'required',
        'media_id'    => 'required',
        'category_id' => 'required',
//        'created_at' => 'required',
//        'updated_at' => 'required',
//        'deleted_at' => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
//        'id' => 'required',
//        'user_id' => 'required',
        'media_id' => 'required',
//        'created_at' => 'required',
//        'updated_at' => 'required',
//        'deleted_at' => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
//        'id' => 'required',
//        'user_id' => 'required',
        'media_id'    => 'sometimes',
        'category_id' => 'required',
//        'created_at' => 'required',
//        'updated_at' => 'required',
//        'deleted_at' => 'required'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
//        'id' => 'required',
//        'user_id' => 'required',
        'media_id'    => 'sometimes',
        'category_id' => 'required',
//        'created_at' => 'required',
//        'updated_at' => 'required',
//        'deleted_at' => 'required'
    ];

//    public function media()
//    {
//        return $this->belongsTo(Media::class, 'media_id', 'id');
//    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
