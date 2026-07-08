<?php

namespace App\Jobs;

use App\Helper\FileHelper;
use App\Helper\GenerateMixSoundHelper;
use App\Helper\Util;
use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class GenerateMixSound implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $media;
    protected $mixer;
    protected $generateMixSoundHelper;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media, array $mixer)
    {
        $this->media = $media;
        $this->mixer = $mixer;
        $this->generateMixSoundHelper = new GenerateMixSoundHelper();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('GenerateMixSound::JOB START | FOR Media ID #:'.$this->media->id);
        $mixAudioTempPath = $this->generateMixSoundHelper->createMixSound($this->mixer);

        $mediaDuration = $this->generateMixSoundHelper->getMediaDuration($mixAudioTempPath);
        $mediaDurationInSeconds = Util::timeToSeconds($mediaDuration);

        if ($mixAudioTempPath && file_exists($mixAudioTempPath)) {

            Log::info('Mix Media #'.$this->media->id.' Sound is going to upload on S3');
            $mixAudioFileS3Url = FileHelper::s3Upload($mixAudioTempPath);

            if($mixAudioFileS3Url){
                Log::info('Mix Media #'.$this->media->id.' Sound is uploaded on S3 | Mix Sound S3 Url is: ' . $mixAudioFileS3Url);
                $mixMediaIsUpdated = Media::find($this->media->id)->update([
                    'file_url' => $mixAudioFileS3Url,
                    'duration' => $mediaDurationInSeconds ?? config('constants.default_mixer_audio_length')
                ]);

                FileHelper::deleteFileIfExists($mixAudioTempPath);

                if ($mixMediaIsUpdated) {
                    Log::info('Mix Media #'.$this->media->id.' Sound is updated');
                }else{
                    Log::warning('Mix Media #'.$this->media->id.' Sound is not updated');
                }
            }
        }

        Log::info('GenerateMixSound::JOB END | FOR Media ID #:'.$this->media->id);
    }
}
