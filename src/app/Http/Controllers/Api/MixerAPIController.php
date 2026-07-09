<?php

namespace App\Http\Controllers\Api;

use App\Helper\Util;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\MixerAPIRequest;
use App\Http\Requests\Api\MixerUpdateAPIRequest;
use App\Models\Mixer;
use App\Models\Media;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateMixSound;

class MixerAPIController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', \config('constants.limit'));

        $mixerCategory = Util::getMixerCategory();
        $media = Media::query()->where('category_id', $mixerCategory->id);

        if($request->my_mixer){
            $media->where('user_id', Auth::id());
        }
        
        $media = $media->paginate($limit);

        return $this->sendResponse($media, 'Media retrieved successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(MixerAPIRequest $request)
    {
        $mixerCategory = Util::getMixerCategory();

        try{
            $inputs = $request->only('name', 'image', 'medias');
            
            $mixerMedia = Media::create([
                'user_id' => Auth::id(),
                'category_id' => $mixerCategory->id,
                'name' => $inputs['name'],
                'image' => $inputs['image'],
                'is_mixer' => 1
            ]);

            $mixerSounds = collect($inputs['medias'])->map(function($media) use($mixerMedia) {
                return [
                    'media_id' => $mixerMedia->id,
                    'orignal_media_id' => $media['id'],
                    'name' => $media['name'],
                    'volume' => $media['volume'] ?? 1,
                    'image' => $media['image'],
                    'file_url' => $media['file_url'],
                    'duration' => $media['duration'] ?? config('constants.default_mixer_audio_length')
                ];
            })->toArray();

            $mixerSounds = $mixerMedia->mixers()->createMany($mixerSounds);
            GenerateMixSound::dispatch($mixerMedia, $mixerSounds->toArray());

            $mixerMedia = Media::find($mixerMedia->id);

            return $this->sendResponse($mixerMedia, 'Mixer-Sound created successfully');

        }catch(\Exception $e){
            return $this->sendErrorWithData([$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Mixer  $mixer
     * @return \Illuminate\Http\Response
     */
    public function show(Mixer $mixer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Mixer  $mixer
     * @return \Illuminate\Http\Response
     */
    public function edit(Mixer $mixer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mixer  $mixer
     * @return \Illuminate\Http\Response
     */
    public function update(MixerUpdateAPIRequest $request, $mediaId)
    {
        $inputs = $request->only('name', 'image', 'medias');
        
        try{
            $media = Media::find($mediaId);
            
            if(!$media)
                throw new Error('Mixer not found!');

            if($media->user_id !== Auth::id())
                throw new Error('You are not able to update this mixer');

            $media->update([
                'name' => $inputs['name'],
                'image' => $inputs['image'],
                'is_mixer' => 1
            ]);

            $mixerSounds = collect($inputs['medias'])->map(function($inputMedia) use($media) {
                return [
                    'media_id' => $media->id,
                    'orignal_media_id' => $inputMedia['orignal_media_id'],
                    'name' => $inputMedia['name'],
                    'volume' => $inputMedia['volume'] ?? 1,
                    'image' => $inputMedia['image'],
                    'file_url' => $inputMedia['file_url'],
                    'duration' => $inputMedia['duration'] ?? config('constants.default_mixer_audio_length')
                ];
            })->toArray();

            DB::statement('DELETE FROM `mixers` WHERE media_id = '.$media->id);

            $mixerSounds = $media->mixers()->createMany($mixerSounds);
            GenerateMixSound::dispatch($media, $mixerSounds->toArray());

            $media = Media::find($media->id);

            return $this->sendResponse($media, 'Mixer-Sound created successfully');

        }catch(\Error $e){
            return $this->sendErrorWithData([$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Mixer  $mixer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $mediaId)
    {
        try{
            $media = Media::find($mediaId);
            
            if(!$media)
                throw new Error('Mixer not found!');

            if($media->user_id !== Auth::id())
                throw new Error('You are not able to update this mixer');
        
            $media->mixers()->delete();
            $media->delete();

            return $this->sendResponse(null, 'Mixer-Sound created successfully');

        }catch(\Error $e){
            return $this->sendErrorWithData([$e->getMessage()]);
        }
    }
}
