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
 *      definition="Order",
 *      required={"user_id", "status", "total_amount", "datetime"},
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
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="total_amount",
 *          description="total_amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="datetime",
 *          description="datetime",
 *          type="string",
 *          format="date-time"
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
class Order extends Model
{
    use SoftDeletes;

    public $table = 'orders';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'user_id',
        'status',
        'total_amount',
        'datetime',
        'donated_to_id',
        'card_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'card_id'       => 'integer',
        'donated_to_id' => 'integer',
        'user_id'       => 'integer',
        'status'        => 'integer',
        'total_amount'  => 'float',
        'datetime'      => 'datetime'
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
        'user_id'      => 'required',
        'status'       => 'required',
        'total_amount' => 'required',
        'card_id'      => 'sometimes',
        'datetime'     => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'user_id'      => 'required',
        'status'       => 'required',
        'total_amount' => 'required',
        'datetime'     => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
//        'user_id' => 'required',
        'datetime'      => 'required',
        'total_amount'  => 'required',
        'card_id'       => 'sometimes',
        'donated_to_id' => 'required',
        'text'          => 'required|max:150',
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        'user_id'      => 'required',
        'status'       => 'required',
        'total_amount' => 'required',
        'datetime'     => 'required',
        'text'         => 'required|max:150',
    ];


}
