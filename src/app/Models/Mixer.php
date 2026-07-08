<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mixer extends Model
{
    use SoftDeletes;

    public $fillable = [
        'media_id',
        'orignal_media_id',
        'name',
        'volume',
        'image',
        'file_url',
        'duration',
    ];
}
