<?php

namespace App\Jobs;

use App\Helper\Util;
use App\Models\Media;
use Aws\MediaConvert\MediaConvertClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckAWSMediaConvert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Media */
    protected $media;

    /**
     * Create a new job instance.
     *
     * @param Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new MediaConvertClient(config('constants.aws.credentials'));
        $result = $client->getJob(['Id' => $this->media->media_convert_job]);
        $job    = $result->get('Job');
        if ($job['Status'] == "PROGRESSING") {
            self::dispatch($this->media)->delay(now()->addMinutes(Util::getDispatchInterval($job['StatusUpdateInterval'])));
        } else if ($job['Status'] == "COMPLETE") {
            $this->media->drm_ios     = Util::getEncryptedPath($this->media, Media::SUFFIX_IOS, false);
            $this->media->drm_android = Util::getEncryptedPath($this->media, Media::SUFFIX_ANDROID, false);
            $this->media->save();
        }
    }

}
