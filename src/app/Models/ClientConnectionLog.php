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
 * @property mixed seconds_until_next
 * @property mixed status
 *
 * @SWG\Definition(
 *      definition="ClientConnectionLog",
 *      required={"client_id", "status", "seconds_until_next"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="client_id",
 *          description="client_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="seconds_until_next",
 *          description="seconds_until_next",
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
class ClientConnectionLog extends Model
{
    use SoftDeletes;

    public $table = 'client_connection_logs';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'client_id',
        'status',
        'seconds_until_next'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                 => 'integer',
        'client_id'          => 'integer',
        'status'             => 'boolean',
        'seconds_until_next' => 'integer'
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


    protected $attributes = [
        'seconds_until_next' => 0
    ];

    /**
     * Validation create rules
     *
     * @var array
     */
    public static $rules = [
        'client_id'          => 'required',
        'status'             => 'required',
        'seconds_until_next' => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'client_id'          => 'required',
        'status'             => 'required',
        'seconds_until_next' => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        'client_id'          => 'required',
        'status'             => 'required',
        'seconds_until_next' => 'required'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        'client_id'          => 'required',
        'status'             => 'required',
        'seconds_until_next' => 'required'
    ];


    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
