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
 *      definition="Transaction",
 *      required={"user_id", "order_id", "currency", "amount", "description", "status"},
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
 *          property="order_id",
 *          description="order_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="string"
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
class Transaction extends Model
{
    // use SoftDeletes;

    public $table = 'transactions';


    const CREDIT = 1;
    const DEBIT  = 2;

    const     PM_GIFTCARD  = 1;
    const     PM_USERCARD  = 2;
    const     PM_WALLET    = 3;
    const     PM_OTHER     = 4;
    const     PM_TOKEN     = 5;
    const     SUBSCRIPTION = 6;

    const STATUS_TYPE     = 'succeeded';
    const PAY_TYPE_PAYPAL = 20;
    const PAY_TYPE_STRIPE = 10;
    protected $dates = ['deleted_at'];


    public $fillable = [
        'user_id',
        'type',
        'job_id',
        'transaction_id',
        'transaction_date',
        'currency',
        'amount',
        'description',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'user_id'           => 'integer',
        'type'              => 'integer',
        'job_id'            => 'integer',
        'transaction_id'    => 'string',
        'transaction_date'  => 'date',
        'currency'          => 'string',
        'amount'            => 'float',
        'description'       => 'string',
        'status'            => 'string',
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
    protected $appends = [
        'total_spend', 'status_badge'
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
        'user_id'           => 'sometimes',
        'type'              => 'sometimes',
        'job_id'            => 'sometimes',
        'transaction_id'    => 'sometimes',
        'transaction_date'  => 'sometimes',
        'currency'          => 'sometimes',
        'amount'            => 'sometimes',
        'description'       => 'sometimes',
        'status'            => 'sometimes'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'user_id'           => 'sometimes',
        'type'              => 'sometimes',
        'job_id'            => 'sometimes',
        'transaction_id'    => 'sometimes',
        'transaction_date'  => 'sometimes',
        'currency'          => 'sometimes',
        'amount'            => 'sometimes',
        'description'       => 'sometimes',
        'status'            => 'sometimes'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        'user_id'               => 'required',
        'type'                  => 'sometimes',
        'job_id'                => 'sometimes',
        'transaction_receipt'   => 'required',
        'transaction_date'      => 'required',
        'currency'              => 'required',
        'amount'                => 'required',
        'description'           => 'required',
        'status'                => 'required'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        'user_id'               => 'required',
        'type'                  => 'sometimes',
        'job_id'                => 'sometimes',
        'transaction_receipt'   => 'required',
        'transaction_date'      => 'required',
        'currency'              => 'required',
        'amount'                => 'required',
        'description'           => 'required',
        'status'                => 'required'
    ];

    public function getTotalSpendAttribute()
    {
        $sent = Transaction::where('user_id', \Auth::id())->sum('amount');
        return $sent;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getStatusBadgeAttribute()
    {
        $statusBadge = null;
        switch ($this->status) {
            case Transaction::STATUS_TYPE:
                $statusBadge = '<span class="label label-success">Succeeded</span>';
                break;
            default:
                $statusBadge = '<span class="label label-danger">Rejected</span>';
                break;
        }
        
        return $statusBadge;
    }

}
