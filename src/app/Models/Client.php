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
 * @property mixed status
 * @property mixed connection_status
 * @property mixed connection_limit
 *
 * @SWG\Definition(
 *      definition="Client",
 *      required={"name", "cidr", "mac", "connection_limit", "license", "connection_status", "status", "status_message"},
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
 *          property="cidr",
 *          description="cidr",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="mac",
 *          description="mac",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="connection_limit",
 *          description="connection_limit",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="license",
 *          description="license",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="connection_status",
 *          description="connection_status",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="status_message",
 *          description="status_message",
 *          type="string"
 *      )
 * )
 */
class Client extends Model
{
    use SoftDeletes;

    public $table = 'clients';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'cidr',
        'mac',
        'connection_limit',
        'license',
        'status',
        'status_message'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name'              => 'string',
        'cidr'              => 'string',
        'mac'               => 'string',
        'connection_limit'  => 'integer',
        'license'           => 'string',
        'connection_status' => 'boolean',
        'status'            => 'boolean',
        'status_message'    => 'string'
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
        'name'             => 'required',
        'cidr'             => 'required',
        'mac'              => 'required',
        'connection_limit' => 'required',
//        'license'        => 'required',
        'status'           => 'required',
//        'status_message' => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'name'             => 'required',
        'cidr'             => 'required',
        'mac'              => 'required',
        'connection_limit' => 'required',
//        'license'        => 'required',
        'status'           => 'required',
//        'status_message' => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        'name'             => 'required',
        'cidr'             => 'required',
        'mac'              => 'required',
        'connection_limit' => 'required',
//        'license'        => 'required',
        'status'           => 'required',
        'status_message'   => 'required_if:status,false'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        'name'             => 'required',
        'cidr'             => 'required',
        'mac'              => 'required',
        'connection_limit' => 'required',
//        'license'        => 'required',
        'status'           => 'required',
//        'status_message' => 'required'
    ];


}
