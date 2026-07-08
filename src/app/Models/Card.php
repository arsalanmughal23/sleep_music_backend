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
 *      definition="Card",
 *      required={"payment_method", "user_id"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payment_method",
 *          description="payment_method",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="last_four",
 *          description="last_four",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="country",
 *          description="country",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="brand",
 *          description="brand",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="exp_year",
 *          description="exp_year",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_default",
 *          description="is_default",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="exp_month",
 *          description="exp_month",
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
class Card extends Model
{
    use SoftDeletes;

    public $table = 'cards';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'payment_method',
        'user_id',
        'last_four',
        'country',
        'brand',
        'exp_year',
        'exp_month'

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'payment_method' => 'string',
        'user_id'        => 'integer',
        'last_four'      => 'integer',
        'country'        => 'string',
        'brand'          => 'string',
        'exp_year'       => 'integer',
        'is_default'     => 'integer',
        'exp_month'      => 'integer'
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
        'payment_method' => 'required',
        'user_id'        => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'payment_method' => 'required',
        'user_id'        => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        'payment_method' => 'required',
        'user_id'        => 'sometimes'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        'payment_method' => 'required',
        'user_id'        => 'sometimes'
    ];


}
