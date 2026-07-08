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
 *      definition="Package",
 *      required={"name", "price", "currency", "status", "is_default", "created_at"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="price",
 *          description="price",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="product_min_limit",
 *          description="product_min_limit",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="product_max_limit",
 *          description="product_max_limit",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="package_id_ios",
 *          description="package_id_ios",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="package_id_android",
 *          description="package_id_android",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="is_default",
 *          description="is_default",
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
class Package extends Model
{
    use SoftDeletes;

    public $table = 'packages';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'name',
        'price',
        'currency',
        'product_min_limit',
        'product_max_limit',
        'package_id_ios',
        'package_id_android',
        'is_default'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                 => 'integer',
        'name'               => 'string',
        'price'              => 'float',
        'currency'           => 'string',
        'product_max_limit'  => 'integer',
        'product_min_limit'  => 'integer',
        'package_id_ios'     => 'string',
        'package_id_android' => 'string',
        'status'             => 'boolean',
        'is_default'         => 'boolean'
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
        'name'       => 'required',
        'price'      => 'required',
        'currency'   => 'required',
        'status'     => 'required',
        'is_default' => 'required',
        'created_at' => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'name'       => 'required',
        'price'      => 'required',
        'currency'   => 'required',
        'status'     => 'required',
        'is_default' => 'required',
        'created_at' => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        'name'       => 'required',
        'price'      => 'required',
        'currency'   => 'required',
        'status'     => 'required',
        'is_default' => 'required',
        'created_at' => 'required'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        'name'       => 'required',
        'price'      => 'required',
        'currency'   => 'required',
        'status'     => 'required',
        'is_default' => 'required',
        'created_at' => 'required'
    ];


}
