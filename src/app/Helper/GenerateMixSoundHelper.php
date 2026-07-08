<?php

namespace App\Helper;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class GenerateMixSoundHelper
 * @package App\Helper
 */
class GenerateMixSoundHelper
{
    protected $ffmepgPath;
    protected $ffmpegCommand;

    public function __construct()
    {
        $this->ffmepgPath = config('constants.ffmpeg_bin_path');

        if(!$this->ffmepgPath){
            throw new \Exception('Make sure FFmpeg is must be installed on server & set FFmpeg path in env');
        }

        // if(!file_exists($this->ffmepgPath)){
        //     throw new \Exception('FFmpeg is not available on provided path');
        // }

        $this->ffmpegCommand = $this->ffmepgPath;

        $cmdExecutedOutput = $this->executeFFmpegCommand($this->ffmpegCommand.' -version');
        if(!$cmdExecutedOutput['status']){
            throw new \Exception('FFmpeg is not ready to execute');
        }
    }

    public function getMediaDuration($filePath)
    {
        $command = $this->ffmpegCommand;
        // $command = 'C:/ffmpeg/bin/ffmpeg -i E:\arsalan\laragon\www\sleep-meditation\storage\app\test_audios\temp_mixAudio_file_65cccc4a84aa3_v2.mp3 2>&1';
        $command .= ' -i '.$filePath.' 2>&1';
        $commandExecutedOutput = exec($command, $output, $returnCode);
        $duration = null;

        foreach ($output as $line) {
            if (strpos($line, 'Duration') !== false) {
                // Extract the duration from the line
                $durationParts = explode(',', $line);
                $durationPart = explode('Duration:', $durationParts[0]);
                $duration = trim(end($durationPart));
            }
        }
        return $duration;
    }

    public function createMixSound($medias)
    {
        Log::info('GenerateMixSoundHelper::HelperFunction START');
        $command = $this->ffmpegCommand;

        $mixerFilesCount = 0;
        $command_files_part = '';
        $command_filter_complex_part = '';
        $command_filter_complex_part2 = '';
        $mixerSounds = [];

        $temporaryFiles = [
            'audios' => [],
            'mix_audio' => null
        ];

        $mixAudioLength = Util::getMixAudioLength();
        foreach($medias as $index => $media){
            Log::info(' ==> File '.($index+1).' are downloading & prepare executable command');

            $mixAudioLength = $media['duration'] > $mixAudioLength ? $media['duration'] : $mixAudioLength;

            // $fileUrl = storage_path('app\temp') . '\\' . $index . '.mp3';
            $fileUrl = FileHelper::downloadAndSaveFile($media['file_url']);
            array_push($temporaryFiles['audios'], $fileUrl);

            $mixerFilesCount++;
            $command_files_part = $command_files_part . ' -i "' . $fileUrl . '"';
            $command_filter_complex_part = $command_filter_complex_part . '['.$index.':a]volume='.($media['volume'] ?? 1).',aloop=loop=-1:size=1e9,asetpts=N/SR/TB[a'.$index.'loop];';
            $command_filter_complex_part2 = $command_filter_complex_part2 . '[a'.$index.'loop]';

            array_push($mixerSounds, [
                'name' => $media['name'],
                'volume' => $media['volume'] ?? 1,
                'image' => $media['image'],
                'file_url' => $media['file_url']
            ]);
        }

        $outputFile = storage_path('app\temp') . '\\' . uniqid('temp_mixAudio_file_') . '.mp3';
        $command = $command . $command_files_part . ' -filter_complex "' . $command_filter_complex_part . $command_filter_complex_part2;
        $command = $command . 'amix=inputs='.$mixerFilesCount.':duration=longest:dropout_transition='.$mixerFilesCount.'[out]"';
        $command = $command . ' -map "[out]" -t '.$mixAudioLength.' "' . $outputFile . '"';

        Log::info(' ==> Command is prepared & proceed to execute');
        $cmdExecutedOutput = $this->executeFFmpegCommand($command);
        $mixAudioTemporaryFilePath = $cmdExecutedOutput['status'] ? $outputFile : null;
        Log::info(' ==> Command is executed');

        if($mixAudioTemporaryFilePath != null){
            Log::info(' ==> Mix Audio is Generated & Temp Path is: '. $outputFile);
            
            foreach($temporaryFiles['audios'] as $temporaryPathAudioFile){
                FileHelper::deleteFileIfExists($temporaryPathAudioFile);
            }
        }else{
            Log::alert(' ==> Mix Audio is not Generated');
        }

        $temporaryFiles['mix_audio'] = $mixAudioTemporaryFilePath;

        Log::info('GenerateMixSoundHelper::HelperFunction END');

        return $temporaryFiles['mix_audio'];
    }

    private function executeFFmpegCommand($command)
    {
        Log::alert('COMMAND: ' . $command);
        // Execute the FFmpeg command
        $commandExecutedOutput = exec($command, $output, $returnCode);

        // Check if the command executed successfully returnCode === 0
        return [
            'status' => $returnCode === 0 ? true : false,
            'command' => $command,
            'output' => $output,
            'returnCode' => $returnCode,
            'commandExecutedOutput' => $commandExecutedOutput, 
        ];
    }

    public function optimizeServerTempFile($file)
    {
        $originalName = $file->getClientOriginalName();
        $originalNameParts = explode('.', $originalName);
        $extension = end($originalNameParts);
        
        $fileAbsolutePath = Storage::disk('local')->put('temp', $file);
        $fileStoragePath = storage_path('app/'.$fileAbsolutePath);

        Log::info(' ==> File is Optimizing');
        $optimizedOutputFileAbsolutePath = 'temp/' . uniqid('temp_audio_optimized_file_') . '.' . $extension;
        $optimizedOutputFileStoragePath = storage_path('app/'.$optimizedOutputFileAbsolutePath);
        $optimizingCmd = $this->ffmepgPath.' -i '.$fileStoragePath.' -b:a 128k '.$optimizedOutputFileStoragePath;
        $cmdExecutedOutput = $this->executeFFmpegCommand($optimizingCmd);
        Log::info(' ==> File is Optimized');

        if ($cmdExecutedOutput['status']) {
            Storage::disk('local')->delete($fileAbsolutePath);
            return $optimizedOutputFileAbsolutePath;
        }

        return null;
    }
}