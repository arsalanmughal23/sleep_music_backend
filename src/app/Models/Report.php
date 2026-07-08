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
 *      definition="Report",
 *      required={"user_id", "music_id", "report_type"},
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
 *          property="music_id",
 *          description="music_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="report_type",
 *          description="report_type",
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
class Report extends Model
{
    use SoftDeletes;

    public $table = 'report';

    const INSTANCE_TYPE_ACCOUNT = 10;
    const INSTANCE_TYPE_CONTENT = 20;
    static    $INSTANCE_TYPES = [
        self::INSTANCE_TYPE_ACCOUNT => "Account",
        self::INSTANCE_TYPE_CONTENT => "Content",
    ];
    protected $dates          = ['deleted_at'];


    public $fillable = [
        'user_id',
        'instance_type',
        'instance_id',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                  => 'integer',
        'user_id'             => 'integer',
        'media_id'            => 'integer',
    ];

    /**
     * The objects that should be append to toArray.
     *
     * @var array
     */
    protected $with = [
        'user',
        'types',
        'media'
    ];

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
        'user_id'             => 'required',
        'media_id'            => 'required',
        'report_type'         => 'required',
        'report_user_content' => 'required'
    ];

    /**
     * Validation update rules
     *
     * @var array
     */
    public static $update_rules = [
        'user_id'             => 'required',
        'media_id'            => 'required',
        'report_type'         => 'required',
        'report_user_content' => 'required'
    ];

    /**
     * Validation api rules
     *
     * @var array
     */
    public static $api_rules = [
        'instance_id'    => 'required|integer|exists:media,id,deleted_at,NULL',
        'report_type_ids' => 'array|min:1',
        'report_type_ids.*' => 'integer|exists:report_type,id,deleted_at,NULL',
        'description' => 'nullable|max:500'
    ];

    /**
     * Validation api update rules
     *
     * @var array
     */
    public static $api_update_rules = [
        'instance_id'    => 'nullable|integer|exists:media,id,deleted_at,NULL',
        'report_type_ids' => 'array',
        'report_type_ids.*' => 'integer|exists:report_type,id,deleted_at,NULL',
        'description' => 'nullable|max:500'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function types()
    {
        return $this->belongsToMany(ReportType::class, 'user_report_types', 'report_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'instance_id', 'id');
    }
}
