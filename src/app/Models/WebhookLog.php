<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookLog extends Model
{
    // use SoftDeletes;

    public $table = 'webhook_logs';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'user_id',
        'transactionId',
        'originalTransactionId',
        'notificationType',
        'subType',
        'productId',
        'expiresDate',
        'type',
        'offerDiscountType',
        'platform',
        'data',
        'signedPayload',
        'error',
        'logs',
        // 'transaction_id',
        // 'original_transaction_id',
        // 'notification_type',
        // 'sub_type',
    ];

    public $casts = [
        'data' => 'json',
        'logs' => 'json'
    ];

    public $hidden = ['signedPayload', 'data', 'logs'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
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

    protected $visible = [];

}
