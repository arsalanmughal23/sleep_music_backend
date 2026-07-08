<?php

namespace App\Jobs;

use App\Helper\Util;
use App\Models\Media;
use Aws\Exception\AwsException;
use Aws\MediaConvert\MediaConvertClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchAWSMediaConvert implements ShouldQueue
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
        $client     = new MediaConvertClient(config('constants.aws.credentials'));
        $jobSetting = [
            "OutputGroups"  => [
                [
                    "CustomName"          => "AppleHLS-HD",
                    "Name"                => "Apple HLS",
                    "Outputs"             => [
                        [
                            "ContainerSettings" => [
                                "Container"    => "M3U8",
                                "M3u8Settings" => [
                                    "AudioFramesPerPes"  => 4,
                                    "PcrControl"         => "PCR_EVERY_PES_PACKET",
                                    "PmtPid"             => 480,
                                    "PrivateMetadataPid" => 503,
                                    "ProgramNumber"      => 1,
                                    "PatInterval"        => 0,
                                    "PmtInterval"        => 0,
                                    "Scte35Source"       => "NONE",
                                    "NielsenId3"         => "NONE",
                                    "TimedMetadata"      => "NONE",
                                    "VideoPid"           => 481,
                                    "AudioPids"          => [
                                        482,
                                        483,
                                        484,
                                        485,
                                        486,
                                        487,
                                        488,
                                        489,
                                        490,
                                        491,
                                        492
                                    ]
                                ]
                            ],
                            "VideoDescription"  => [
                                "ScalingBehavior"   => "DEFAULT",
                                "TimecodeInsertion" => "DISABLED",
                                "AntiAlias"         => "ENABLED",
                                "Sharpness"         => 50,
                                "CodecSettings"     => [
                                    "Codec"        => "H_264",
                                    "H264Settings" => [
                                        "InterlaceMode"                       => "PROGRESSIVE",
                                        "NumberReferenceFrames"               => 3,
                                        "Syntax"                              => "DEFAULT",
                                        "Softness"                            => 0,
                                        "GopClosedCadence"                    => 1,
                                        "GopSize"                             => 90,
                                        "Slices"                              => 1,
                                        "GopBReference"                       => "DISABLED",
                                        "SlowPal"                             => "DISABLED",
                                        "SpatialAdaptiveQuantization"         => "ENABLED",
                                        "TemporalAdaptiveQuantization"        => "ENABLED",
                                        "FlickerAdaptiveQuantization"         => "DISABLED",
                                        "EntropyEncoding"                     => "CABAC",
                                        "Bitrate"                             => 647000,
                                        "FramerateControl"                    => "INITIALIZE_FROM_SOURCE",
                                        "RateControlMode"                     => "CBR",
                                        "CodecProfile"                        => "MAIN",
                                        "Telecine"                            => "NONE",
                                        "MinIInterval"                        => 0,
                                        "AdaptiveQuantization"                => "HIGH",
                                        "CodecLevel"                          => "AUTO",
                                        "FieldEncoding"                       => "PAFF",
                                        "SceneChangeDetect"                   => "ENABLED",
                                        "QualityTuningLevel"                  => "SINGLE_PASS_HQ",
                                        "FramerateConversionAlgorithm"        => "DUPLICATE_DROP",
                                        "UnregisteredSeiTimecode"             => "DISABLED",
                                        "GopSizeUnits"                        => "FRAMES",
                                        "ParControl"                          => "INITIALIZE_FROM_SOURCE",
                                        "NumberBFramesBetweenReferenceFrames" => 2,
                                        "RepeatPps"                           => "DISABLED",
                                        "DynamicSubGop"                       => "STATIC"
                                    ]
                                ],
                                "AfdSignaling"      => "NONE",
                                "DropFrameTimecode" => "ENABLED",
                                "RespondToAfd"      => "NONE",
                                "ColorMetadata"     => "INSERT"
                            ],
                            "OutputSettings"    => [
                                "HlsSettings" => [
                                    "AudioGroupId"       => "program_audio",
                                    "AudioRenditionSets" => "program_audio",
                                    "AudioOnlyContainer" => "AUTOMATIC",
                                    "IFrameOnlyManifest" => "EXCLUDE"
                                ]
                            ],
                            "NameModifier"      => "ios_video"
                        ],
                        [
                            "ContainerSettings" => [
                                "Container"    => "M3U8",
                                "M3u8Settings" => [
                                    "AudioFramesPerPes"  => 4,
                                    "PcrControl"         => "PCR_EVERY_PES_PACKET",
                                    "PmtPid"             => 480,
                                    "PrivateMetadataPid" => 503,
                                    "ProgramNumber"      => 1,
                                    "PatInterval"        => 0,
                                    "PmtInterval"        => 0,
                                    "Scte35Source"       => "NONE",
                                    "NielsenId3"         => "NONE",
                                    "TimedMetadata"      => "NONE",
                                    "VideoPid"           => 481,
                                    "AudioPids"          => [
                                        482,
                                        483,
                                        484,
                                        485,
                                        486,
                                        487,
                                        488,
                                        489,
                                        490,
                                        491,
                                        492
                                    ]
                                ]
                            ],
                            "AudioDescriptions" => [
                                [
                                    "AudioTypeControl"    => "FOLLOW_INPUT",
                                    "AudioSourceName"     => "Audio Selector 1",
                                    "CodecSettings"       => [
                                        "Codec"       => "AAC",
                                        "AacSettings" => [
                                            "AudioDescriptionBroadcasterMix" => "NORMAL",
                                            "Bitrate"                        => 96000,
                                            "RateControlMode"                => "CBR",
                                            "CodecProfile"                   => "LC",
                                            "CodingMode"                     => "CODING_MODE_2_0",
                                            "RawFormat"                      => "NONE",
                                            "SampleRate"                     => 48000,
                                            "Specification"                  => "MPEG4"
                                        ]
                                    ],
                                    "LanguageCodeControl" => "FOLLOW_INPUT"
                                ]
                            ],
                            "OutputSettings"    => [
                                "HlsSettings" => [
                                    "AudioGroupId"       => "program_audio",
                                    "AudioOnlyContainer" => "AUTOMATIC",
                                    "IFrameOnlyManifest" => "EXCLUDE"
                                ]
                            ],
                            "NameModifier"      => "ios_audio"
                        ]
                    ],
                    "OutputGroupSettings" => [
                        "Type"             => "HLS_GROUP_SETTINGS",
                        "HlsGroupSettings" => [
                            "ManifestDurationFormat" => "INTEGER",
                            "SegmentLength"          => 10,
                            "TimedMetadataId3Period" => 10,
                            "CaptionLanguageSetting" => "OMIT",
                            "Destination"            => "s3://nodaapp/encrypted/ios_2/",
                            "Encryption"             => [
                                "EncryptionMethod"               => "AES128",
                                "InitializationVectorInManifest" => "INCLUDE",
                                "OfflineEncrypted"               => "DISABLED",
                                "SpekeKeyProvider"               => [
                                    "ResourceId" => "159-HD_IOS",
                                    "SystemIds"  => [
                                        "94ce86fb-07ff-4f43-adb8-93d2fa968ca2"
                                    ],
                                    "Url"        => "https://0yxwb29ci8.execute-api.us-west-2.amazonaws.com/live/speke/v1.0/copyProtection"
                                ],
                                "Type"                           => "SPEKE"
                            ],
                            "TimedMetadataId3Frame"  => "PRIV",
                            "CodecSpecification"     => "RFC_4281",
                            "OutputSelection"        => "MANIFESTS_AND_SEGMENTS",
                            "ProgramDateTimePeriod"  => 600,
                            "MinSegmentLength"       => 0,
                            "MinFinalSegmentLength"  => 0,
                            "DirectoryStructure"     => "SINGLE_DIRECTORY",
                            "ProgramDateTime"        => "EXCLUDE",
                            "SegmentControl"         => "SEGMENTED_FILES",
                            "ManifestCompression"    => "NONE",
                            "ClientCache"            => "ENABLED",
                            "StreamInfResolution"    => "INCLUDE"
                        ]
                    ]
                ],
                [
                    "CustomName"          => "Widewine-HD",
                    "Name"                => "DASH ISO",
                    "Outputs"             => [
                        [
                            "ContainerSettings" => [
                                "Container" => "MPD"
                            ],
                            "VideoDescription"  => [
                                "ScalingBehavior"   => "DEFAULT",
                                "TimecodeInsertion" => "DISABLED",
                                "AntiAlias"         => "ENABLED",
                                "Sharpness"         => 50,
                                "CodecSettings"     => [
                                    "Codec"        => "H_264",
                                    "H264Settings" => [
                                        "InterlaceMode"                       => "PROGRESSIVE",
                                        "NumberReferenceFrames"               => 3,
                                        "Syntax"                              => "DEFAULT",
                                        "Softness"                            => 0,
                                        "GopClosedCadence"                    => 1,
                                        "GopSize"                             => 90,
                                        "Slices"                              => 1,
                                        "GopBReference"                       => "DISABLED",
                                        "SlowPal"                             => "DISABLED",
                                        "SpatialAdaptiveQuantization"         => "ENABLED",
                                        "TemporalAdaptiveQuantization"        => "ENABLED",
                                        "FlickerAdaptiveQuantization"         => "DISABLED",
                                        "EntropyEncoding"                     => "CABAC",
                                        "Bitrate"                             => 647000,
                                        "FramerateControl"                    => "INITIALIZE_FROM_SOURCE",
                                        "RateControlMode"                     => "CBR",
                                        "CodecProfile"                        => "MAIN",
                                        "Telecine"                            => "NONE",
                                        "MinIInterval"                        => 0,
                                        "AdaptiveQuantization"                => "HIGH",
                                        "CodecLevel"                          => "AUTO",
                                        "FieldEncoding"                       => "PAFF",
                                        "SceneChangeDetect"                   => "ENABLED",
                                        "QualityTuningLevel"                  => "SINGLE_PASS_HQ",
                                        "FramerateConversionAlgorithm"        => "DUPLICATE_DROP",
                                        "UnregisteredSeiTimecode"             => "DISABLED",
                                        "GopSizeUnits"                        => "FRAMES",
                                        "ParControl"                          => "INITIALIZE_FROM_SOURCE",
                                        "NumberBFramesBetweenReferenceFrames" => 2,
                                        "RepeatPps"                           => "DISABLED",
                                        "DynamicSubGop"                       => "STATIC"
                                    ]
                                ],
                                "AfdSignaling"      => "NONE",
                                "DropFrameTimecode" => "ENABLED",
                                "RespondToAfd"      => "NONE",
                                "ColorMetadata"     => "INSERT"
                            ],
                            "NameModifier"      => "android_video"
                        ],
                        [
                            "ContainerSettings" => [
                                "Container" => "MPD"
                            ],
                            "AudioDescriptions" => [
                                [
                                    "AudioTypeControl"    => "FOLLOW_INPUT",
                                    "AudioSourceName"     => "Audio Selector 1",
                                    "CodecSettings"       => [
                                        "Codec"       => "AAC",
                                        "AacSettings" => [
                                            "AudioDescriptionBroadcasterMix" => "NORMAL",
                                            "Bitrate"                        => 96000,
                                            "RateControlMode"                => "CBR",
                                            "CodecProfile"                   => "LC",
                                            "CodingMode"                     => "CODING_MODE_2_0",
                                            "RawFormat"                      => "NONE",
                                            "SampleRate"                     => 48000,
                                            "Specification"                  => "MPEG4"
                                        ]
                                    ],
                                    "LanguageCodeControl" => "FOLLOW_INPUT"
                                ]
                            ],
                            "NameModifier"      => "android_audio"
                        ]
                    ],
                    "OutputGroupSettings" => [
                        "Type"                 => "DASH_ISO_GROUP_SETTINGS",
                        "DashIsoGroupSettings" => [
                            "SegmentLength"   => 30,
                            "Destination"     => "s3://nodaapp/encrypted/android_2/",
                            "Encryption"      => [
                                "PlaybackDeviceCompatibility" => "CENC_V1",
                                "SpekeKeyProvider"            => [
                                    "ResourceId" => "159-HD_ANDROID",
                                    "SystemIds"  => [
                                        "edef8ba9-79d6-4ace-a3c8-27dcd51d21ed"
                                    ],
                                    "Url"        => "https://0yxwb29ci8.execute-api.us-west-2.amazonaws.com/live/speke/v1.0/copyProtection"
                                ]
                            ],
                            "FragmentLength"  => 2,
                            "SegmentControl"  => "SINGLE_FILE",
                            "MpdProfile"      => "MAIN_PROFILE",
                            "HbbtvCompliance" => "NONE"
                        ]
                    ]
                ]
            ],
            "AdAvailOffset" => 0,
            "Inputs"        => [
                [
                    "AudioSelectors" => [
                        "Audio Selector 1" => [
                            "Offset"           => 0,
                            "DefaultSelection" => "DEFAULT",
                            "ProgramSelection" => 1
                        ]
                    ],
                    "VideoSelector"  => [
                        "ColorSpace"    => "FOLLOW",
                        "Rotate"        => "DEGREE_0",
                        "AlphaBehavior" => "DISCARD"
                    ],
                    "FilterEnable"   => "AUTO",
                    "PsiControl"     => "USE_PSI",
                    "FilterStrength" => 0,
                    "DeblockFilter"  => "DISABLED",
                    "DenoiseFilter"  => "DISABLED",
                    "TimecodeSource" => "EMBEDDED",
                ]
            ]
        ];
        // Override Variables in a single code block so that we can change whenever we want.;
        $jobSetting["Inputs"][0]["FileInput"] = "s3://" . config("constants.aws.media_convert_bucket") . "/" . $this->media->file_path;

        $jobSetting["OutputGroups"][0]["OutputGroupSettings"]["HlsGroupSettings"]["Destination"] = Util::getEncryptedPath($this->media, Media::SUFFIX_IOS);

        $jobSetting["OutputGroups"][0]["OutputGroupSettings"]["HlsGroupSettings"]["Encryption"]["SpekeKeyProvider"]["ResourceId"] = $this->media->id . "-HD_IOS";

        $jobSetting["OutputGroups"][1]["OutputGroupSettings"]["DashIsoGroupSettings"]["Destination"] = Util::getEncryptedPath($this->media, Media::SUFFIX_ANDROID);

        $jobSetting["OutputGroups"][1]["OutputGroupSettings"]["DashIsoGroupSettings"]["Encryption"]["SpekeKeyProvider"]["ResourceId"] = $this->media->id . "-HD_ANDROID";

        $jobSetting["OutputGroups"][0]["Outputs"][0]["VideoDescription"]["CodecSettings"]["H264Settings"]["Bitrate"] = 647000;
        $jobSetting["OutputGroups"][1]["Outputs"][0]["VideoDescription"]["CodecSettings"]["H264Settings"]["Bitrate"] = 647000;


        $jobConfig = [
            "Role"                 => config('constants.aws.media_convert_role'),
            "Settings"             => $jobSetting, //JobSettings structure
            "Queue"                => config('constants.aws.media_convert_queue'),
            /*"UserMetadata"         => [
                "Customer" => "Amazon"
            ],*/
            "AccelerationSettings" => [
                "Mode" => "DISABLED"
            ]
        ];
        // We are not catching the exception because in case of exception, laravel will requeue the job automatically.
        $result = $client->createJob($jobConfig);

        // We are not updating drm_ios and drm_android columns because it wont work until the job is completed;
        // Once the job is completed, we will update those fields.
        $this->media->media_convert_job = $result->get('Job')['Id'];
        $this->media->save();

        $job = new CheckAWSMediaConvert($this->media);
        dispatch($job)->delay(now()->addMinutes(Util::getDispatchInterval($result->get('Job')['StatusUpdateInterval'])));
    }
}
