<?php

namespace App\Helper;

use Illuminate\Support\Facades\Log;
use CURLFile;

/**
 * Class FileHelper
 * @package App\Helper
 */

class FileHelper
{
    /**
     * @return string
     */

    public static function s3Upload($file) {
        $s3Meta = config('constants.s3');
        $token = $s3Meta['token'];
        $presignedUrl = $s3Meta['presignedUrl'];
    
        if ($s3Meta && $token && $presignedUrl) {
            try {
                $fileType = mime_content_type($file);
                // frontend: ['audio/mpeg', 'audio/x-m4a', 'audio/wav']
                // backend: ['audio/mpeg', 'audio/x-m4a', 'audio/x-wav'], 

                // Create a JSON payload
                $payload = json_encode([
                    "contentType" => $fileType
                ]);

                // Create cURL handle for the first POST request to get the presigned URL
                $ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_URL, $presignedUrl);
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch1, CURLOPT_HTTPHEADER, [
                    'x-access-token: ' . $token,
                    'Content-Type: application/json',
                ]);
                curl_setopt($ch1, CURLOPT_POSTFIELDS, $payload);
    
                $presignedPostUrlResponse = curl_exec($ch1);
                if ($presignedPostUrlResponse === false) {
                    throw new \Exception('Failed to get presigned URL');
                }
    
                $presignedPostUrl = json_decode($presignedPostUrlResponse, true);
                $key = $presignedPostUrl['data']['result']['fields']['Key'] ?? null;
                $url = $presignedPostUrl['data']['url'] ?? null;

                if (!$key || !$url) {
                    throw new \Exception('Invalid presigned URL response');
                }
    
                // Create cURL handle for the second POST request to upload the file
                $ch1JsonResponse = $presignedPostUrl['data']['result']['fields'];
                $payload2 = [];
                
                foreach($ch1JsonResponse as $objKey => $data){
                    $payload2[$objKey] = $data;
                }

                $payload2['file'] = new CURLFile($file);
                
                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_URL, $presignedPostUrl['data']['result']['url']);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch2, CURLOPT_HTTPHEADER, [
                    'Access-Control-Allow-Origin: *',
                    'Content-Type: multipart/form-data',
                ]);
                curl_setopt($ch2, CURLOPT_POSTFIELDS, $payload2);

                $response = curl_exec($ch2);

                curl_close($ch1);
                curl_close($ch2);

                if ($response !== false) {
                    Log::error('File is successfully uploaded on s3');
                    return config('constants.s3.accelerateUrl') .'/'. $key;
                }
    
                return false;
            } catch (\Exception $error) {
                Log::error('File uploading on s3 is failed | Error: '.$error->getMessage());
                return null;
            }
        }
    
        return null;
    }

    public static function downloadAndSaveFile($fileUrl)
    {
        // Get the path to the temporary directory
        $temporaryDirectory = storage_path('app\temp');

        // Create the temporary directory if it doesn't exist
        if (!file_exists($temporaryDirectory)) {
            mkdir($temporaryDirectory, 0755, true);
        }

        // Download Audio file
        $temp_audio_file = file_get_contents($fileUrl);

        // Attempt to extract the file extension from the content-type header.
        $headers = get_headers($fileUrl, 1);
        $contentType = isset($headers['Content-Type']) ? $headers['Content-Type'] : '';
        $extensionFromContentType = '';

        if (!empty($contentType)) {
            $extensionFromContentType = explode('/', $contentType);
            $extensionFromContentType = end($extensionFromContentType);
        }

        // Generate a unique file name for your temporary file
        $uniqueFileName = uniqid('temp_audio_file_') . '.' . $extensionFromContentType;


        // Create and save the temporary file
        $filePath = $temporaryDirectory . '\\' . $uniqueFileName;
        file_put_contents($filePath, $temp_audio_file);
        return $filePath;
    }
    
    public static function deleteFileIfExists($filePath)
    {
        if (file_exists($filePath)) {
            return unlink($filePath) ? true : false;
        } else {
            return null;
        }
    }
}