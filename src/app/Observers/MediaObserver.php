<?php

namespace App\Observers;

use App\Jobs\DispatchAWSMediaConvert;
use App\Models\Media;

class MediaObserver
{
    /**
     * @param Media $media
     */
    public function created(Media $media)
    {
//        $job = new DispatchAWSMediaConvert($media)`;
//        dispatch($job);
    }
}
