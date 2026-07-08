<?php

namespace App\Helper;

use App\Models\Category;
use App\Models\Media;
use App\Models\Playlist;
use App\Models\Setting;
use Carbon\Carbon;
use Error;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Tzsk\Collage\Facade\Collage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class UtilHelper
 * @package App\Helper
 */
class Util
{
    const SERVER_TYPE_MASTER = 0;
    const SERVER_TYPE_SLAVE  = 10;

    const BOOL_FALSE = 0;
    const BOOL_TRUE  = 1;

    public static $BOOLS = [
        self::BOOL_FALSE => "No",
        self::BOOL_TRUE  => "Yes",
    ];

    public static $BOOLS_CSS = [
        self::BOOL_FALSE => "danger",
        self::BOOL_TRUE  => "success",
    ];

    public static $BOOLS_BG_CSS = [
        self::BOOL_FALSE => "red",
        self::BOOL_TRUE  => "green",
    ];

    public static function getMixerCategory()
    {
        return \App\Models\Category::whereIsMixer(1)->first();
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function getBoolText($value, $trueValue = null, $falseValue = null)
    {
        if ($value) {
            return $trueValue === null ? self::$BOOLS[$value] : $trueValue;
        }
        return $falseValue === null ? self::$BOOLS[$value] : $falseValue;
    }

    /**
     * @param $value
     * @param bool $bg
     * @return mixed
     */
    public static function getBoolCss($value, $bg = false)
    {
        return $bg ? self::$BOOLS_BG_CSS[$value] : self::$BOOLS_CSS[$value];
    }

    public static function getNone()
    {
        return "<i>(Not Set)</i>";
    }

    public static function getDataTableParams()
    {
        return ['responsive' => true,];
    }

    public function readCSV($file, $colName = 0)
    {
        $row             = 0;
        $rows            = $columns = [];
        $autoLineEndings = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', TRUE);

        if (($handle = fopen(public_path() . '/csv/' . $file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                if (count($data) <= 1) {
                    $columns[] = $data;
                    continue;
                }
                $row++;
                if ($row == 1) {
                    $columns[] = $data;
                    continue;
                }
                $rows[] = $data;
            }
            fclose($handle);
        }
        ini_set('auto_detect_line_endings', $autoLineEndings);

        if ($colName) {
            return [
                'rows'    => $rows,
                'columns' => $columns
            ];
        } else {
            return $rows;
        }
    }

    public function updateCSV($file, $data)
    {
        $current_content = $this->readCSV($file, 1);
        $new_data[0]     = $data;
        $new_content     = array_merge($current_content['columns'], $current_content['rows'], $data);

        $fp = fopen(public_path() . '/csv/' . $file, 'w');
        foreach ($new_content as $content) {
            fputcsv($fp, $content);
        }
        fclose($fp);

        return true;
    }

    public function seedWithCSV($file)
    {
        $newData = [];
        $data    = $this->readCSV($file, 1);

        foreach ($data['rows'] as $key => $row) {
            foreach ($row as $keys => $item) {
                $newData[$key][$data['columns'][0][$keys]] = $item;
            }
        }
        return $newData;
    }


    public static function getHeadersFromCsv($file)
    {
        $headers = [];

        $autoLineEndings = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', TRUE);
        if (($handle = fopen(\Storage::url($file), "r")) !== FALSE) {
            $headers = fgetcsv($handle, 0, ",");
            fclose($handle);
        }
        ini_set('auto_detect_line_endings', $autoLineEndings);
        return $headers;
    }

    public static function changeCollation($str)
    {
        return mb_convert_encoding($str, 'UTF-8', 'Windows-1252');
    }

    public static function getDataFromCsv($file, $has_headers = true)
    {
        $autoLineEndings = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', TRUE);
        $i = 0;
        if (($handle = fopen(\Storage::url($file), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                // if csv has headers then ignore the first row.
                if ($has_headers && $i < 1) {
                    $i++;
                    continue;
                }
                yield array_map(array(Util::class, "changeCollation"), $data);
            }

            fclose($handle);
        }
        ini_set('auto_detect_line_endings', $autoLineEndings);
    }

    public static function makeCollage(array $images)
    {
        $new_images = [];
        foreach ($images as $key => $value) {
            try {
                $image = \Storage::get($value);
            } catch (\Exception $e) {
                continue;
            }
            $new_images[] = $image;
        }

        try {
            if (count($new_images) > 0) {
                $image = Collage::make(400, 400)->from($new_images);
                $name  = time() . '.jpg';
                // Save Locally then upload that same file to storage.
                $image->save('storage/app/public/playlist_images/' . $name);
                return \Storage::put('public/playlist_images', new File('storage/app/public/playlist_images/' . $name));
            }
        } catch (\Exception $e) {

        }
        return false;

    }

    public static function getEncryptedPath($media, $suffix, $s3 = true)
    {
        if ($s3) {
            return config("constants.aws.media_convert_destination") . $media->id . $suffix;
        }
        $filename = pathinfo($media->file_path)['filename'];
        switch ($suffix) {
            case Media::SUFFIX_IOS:
                $filename .= ".m3u8";
                break;
            case Media::SUFFIX_ANDROID:
                $filename .= ".mpd";
                break;
        }
        return config("constants.aws.media_convert_destination_suffix") . $media->id . $suffix . $filename;
    }

    public static function getDispatchInterval($statusUpdateInterval)
    {
        // For future cases;
        switch ($statusUpdateInterval) {
            case "SECONDS_60":
                return 1;
                break;
        }
        return 0;
    }

    public static function getUrl($params, $delete = false)
    {
        $request = request();
        if ($delete) {
            foreach ($params as $param) {
                $request->query->remove($param);
            }
            $newQuery = $request->query();
        } else {
            $newQuery = array_merge($request->query(), $params);
        }
        return $request->fullUrlWithQuery($newQuery);
    }

    public static function does_url_exists($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    public static function makePageHtml($content)
    {
        return '<div style="color:white;font-size:2rem;background-color:#0E2851">'.$content.'</div>';
    }

    public static function JWTDecodeInfo($token)
    {
        if(!$token)
            throw new Error('Token is required', 422);

        $tokenParts = explode(".", $token);
        
        if(!isset($tokenParts[1]))
            throw new Error('Unauthentication: Invalid Token', 403);

        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);
        

        if(!$jwtPayload)
            throw new Error('Unauthentication: Invalid Token', 403);

        return $jwtPayload;
    }

    public static function timeToSeconds($timeString)
    {
        // Parse the time string into a timestamp
        $timestamp = strtotime($timeString);

        // Extract the seconds from the timestamp
        $seconds = date('s', $timestamp);

        // Extract the minutes from the timestamp and convert to seconds
        $minutes = date('i', $timestamp) * 60;

        // Extract the hours from the timestamp and convert to seconds
        $hours = date('G', $timestamp) * 3600;

        // Calculate the total seconds
        $totalSeconds = $hours + $minutes + $seconds;

        return $totalSeconds;
    }

    public static function getMixAudioLength() {
        $mixAudioLength = Setting::first()->mix_sound_length ?? 0;

        $minMixerLength = config('constants.min_mixer_audio_length');
        $maxMixerLength = config('constants.max_mixer_audio_length');

        if($mixAudioLength < $minMixerLength || $mixAudioLength > $maxMixerLength)
            $mixAudioLength = config('constants.default_mixer_audio_length');

        return $mixAudioLength;
    }

    public static function MakeRequestLogs($title = 'RequestLogs', Request $request, $responseLogs = null, $extraLogs = []) {
        Log::info($title . ' | BEGIN');
        Log::info('request_url: '. $request->url());
        Log::info('request_fullUrl: '. $request->fullUrl());
        Log::info('request_all: ', $request->all());

        if(is_array($responseLogs)){
            Log::info('responseLogs: ', $responseLogs);
        } else {
            Log::info('responseLogs: '. $responseLogs);
        }
        if(is_array($extraLogs)){
            Log::info('extraLogs: ', $extraLogs);
        } else {
            Log::info('extraLogs: '. $extraLogs);
        }

        Log::info($title . ' | END');

        return [
            'request_url' => $request->url(),
            'request_fullUrl' => $request->fullUrl(),
            'request_all' => $request->all(),
            'responseLogs' => $responseLogs,
            'extraLogs' => $extraLogs
        ];
    }

    public static function JWTDecodeUserInfo($token)
    {
        $jwtPayload = self::JWTDecodeInfo($token);

        if(!$jwtPayload)
            throw new Error('Unauthentication: Invalid Token', 403);

        $expireAt = \Carbon\Carbon::createFromTimestamp($jwtPayload->exp ?? null);
        $userInfo['email'] = $jwtPayload->email ?? null;
        if(isset($jwtPayload->email_verified)) $userInfo['email_verified'] = $jwtPayload->email_verified;
        if(isset($jwtPayload->exp)) $userInfo['expire_at'] = $expireAt;
        
        if($expireAt->isPast())
            throw new Error('Unauthentication: Token is expire', 403);
            
        $response = [
            'status' => 200,
            'success' => true,
            'message' => 'JWT Decoded',
            'data' => $userInfo
        ];

        return $response;
    }

    public static function checkExpiry($created_at, $milisecons)
    {        
        $expireAt = Carbon::parse($created_at)->addMilliseconds($milisecons);
        $now = Carbon::now();
        
        return $now > $expireAt;
    }

    public static function getFilterByType()
    {
        $all        = Util::getUrl(['type' => -1]);
        $audio_only = Util::getUrl(['type' => Category::TYPE_AUDIO]);
//        $video_only = Util::getUrl(['type' => Category::TYPE_VIDEO]);
        return [
            [
                "extend"  => "collection",
                "text"    => "<i class='fa fa-filter'></i> Filter By Type",
                "buttons" => [
                    [
                        "name"   => "all",
                        "text"   => "<i class='fa fa-check'></i> All",
                        "action" => "function(e, dt, button, config) { window.location.href='" . $all . "' }"
                    ],
                    [
                        "name"   => "audio",
                        "text"   => "<i class='fa fa-volume-up'></i> Audio Only",
                        "action" => "function(e, dt, button, config) { window.location.href='" . $audio_only . "' }"
                    ],
//                    [
//                        "name"   => "video",
//                        "text"   => "<i class='fa fa-video-camera'></i> Video Only",
//                        "action" => "function(e, dt, button, config) { window.location.href='" . $video_only . "' }"
//                    ]
                ]
            ]
        ];
    }

    public static function JWTDecode($token)
    {
        return json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
    }
}