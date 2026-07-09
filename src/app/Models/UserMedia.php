<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserMedia extends Model
{
    use SoftDeletes;

    public $table = 'user_media';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'user_id',
        'media_id',
        'flag',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'       => 'integer',
        'user_id'  => 'integer',
        'media_id' => 'integer',
        'flag'     => 'integer',
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


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id', 'id');
    }

}
