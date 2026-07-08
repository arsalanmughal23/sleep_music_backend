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
 */
class UserSubscription extends Model
{
    // use SoftDeletes;

    public $table = 'user_subscription';


    // DEPRECATED IN APP_STORE_CONNECT V2
        // Used in Android-Webhook
            // const SUBSCRIPTION_RENEWED                = 2;
            // const SUBSCRIPTION_CANCELED               = 3;

            // const SUBSCRIPTION_TRIAL                  = 5;
            // const SUBSCRIPTION_EXPIRED                = 13;

    // AVAILABLE IN APP_STORE_CONNECT V2

        // IOS WEBHOOK SUBSCRIPTION NOTIFICATION_TYPE
            const IOS_SUBSCRIBED                = "SUBSCRIBED";
            const IOS_DID_RENEW                 = "DID_RENEW";
            const IOS_EXPIRED                   = "EXPIRED";
            const IOS_DID_CHANGE_RENEWAL_STATUS = "DID_CHANGE_RENEWAL_STATUS";
            const IOS_DID_CHANGE_RENEWAL_PREF   = "DID_CHANGE_RENEWAL_PREF";

        // IOS WEBHOOK SUBSCRIPTION SUB_TYPE
            const IOS_SUBTYPE_INITIAL_BUY = "INITIAL_BUY";
            const IOS_SUBTYPE_RESUBSCRIBE = "RESUBSCRIBE";
            const IOS_AUTO_RENEW_DISABLED = "AUTO_RENEW_DISABLED";
            const IOS_AUTO_RENEW_ENABLED  = "AUTO_RENEW_ENABLED";
            const IOS_VOLUNTARY           = "VOLUNTARY";
            const IOS_DOWNGRADE           = "DOWNGRADE";
            const IOS_UPGRADE             = "UPGRADE";

        const FREE_TRIAL = "FREE_TRIAL";
        
        const STATUS_HOLD      = 10;
        const STATUS_TRIAL     = 20;
        const STATUS_ACTIVE    = 30;
        const STATUS_CANCELLED = 40;
        const STATUS_EXPIRE    = 50;


    protected $dates = ['deleted_at'];

    public $fillable = [
        'id',
        'user_id',
        'is_free_trial',
        'amount',
        'expiry_date',
        'reference_key',
        'transaction_date',
        'currency',
        'platform',
        'data',
        'product_id',
        'original_transaction_id',
        'transaction_receipt',
        'status',
        'created_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                    => 'integer',
        'user_id'               => 'integer',
        'amount'                => 'integer',
        'expiry_date'           => 'datetime',
        'reference_key'         => 'string',
        'transaction_date'      => 'date',
        'currency'              => 'string',
        'platform'              => 'string',
        'data'                  => 'string',
        'product_id'            => 'string',
        'status'                => 'integer',
        'created_at'            => 'date'
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
    protected $appends = ['status_badge', 'offer_discount_type'];

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
        'id'                    => 'required',
        'user_id'               => 'required',
        'amount'                => 'required',
        'expiry_date'           => 'required',
        'reference_key' => 'required',
        'transaction_date'      => 'required',
        'currency'              => 'required',
        'platform'              => 'required',
        'data'                  => 'required',
        'product_id'            => 'string',
        'status'                => 'required',
        'created_at'            => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'id'                    => 'required',
        'user_id'               => 'required',
        'amount'                => 'required',
        'expiry_date'           => 'required',
        'reference_key' => 'required',
        'transaction_date'      => 'required',
        'currency'              => 'required',
        'platform'              => 'required',
        'data'                  => 'required',
        'product_id'            => 'string',
        'status'                => 'required',
        'created_at'            => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        // 'user_id'                   => 'integer',
        'transaction_receipt'       => 'nullable',
        'transaction_id'            => 'required',
        'original_transaction_id'   => 'nullable',
        'product_id'                => 'required',
        'platform'                  => 'required|in:ios',
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        // 'id'                    => 'required',
        // 'user_id'               => 'required',
        'signedPayload'         => 'required|string',
        'amount'                => 'required',
        'currency'              => 'required',
        'platform'              => 'required|in:ios',

        'product_id'            => 'required|string',
        'transaction_date'      => 'required|string',

        'tenure'                => 'required|in:monthly,yearly',

        // 'data'                  => 'required',
        // 'expiry_date'           => 'required',
        // 'status'                => 'required',
        // 'created_at'            => 'required'
    ];

    public function getStatusBadgeAttribute()
    {
        $statusBadge = null;
        switch ($this->status) {
            case UserSubscription::STATUS_HOLD:
                $statusBadge = '<span class="label label-info">Hold</span>';
                break;
            case UserSubscription::STATUS_ACTIVE:
                $statusBadge = '<span class="label label-success">Active</span>';
                break;
            case UserSubscription::STATUS_CANCELLED:
                $statusBadge = '<span class="label label-danger">Cancelled</span>';
                break;
            case UserSubscription::STATUS_EXPIRE:
                $statusBadge = '<span class="label label-warning">Expire</span>';
                break;
            default:
                break;

        }
        
        return $statusBadge;
    }
    public function getOfferDiscountTypeAttribute()
    {
        return $this->is_free_trial ? '<span class="label label-info">'.UserSubscription::FREE_TRIAL.'</span>' : '-';
    }

    public static function matchSubscriptionName($subscriptionName, $tenure)
    {
        return strpos(request()->get($subscriptionName), $tenure) !== false;
    }

    public static function getMonthsCountByTenure($tenure)
    {
        switch($tenure){
            case 'yearly':
                return 12;
                break;
            case 'monthly':
                return 1;
                break;
            default :
                return 0;
                break;
        }    
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'reference_key', 'transaction_id');
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'transaction_id', 'reference_key');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
