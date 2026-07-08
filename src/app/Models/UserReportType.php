<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReportType extends Model
{
    public $table = 'user_report_types';

    public $fillable = [
        'report_id',
        'report_type_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'report_id'         => 'integer',
        'report_type_id'    => 'integer',
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
    public static $rules = [];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [];

}
