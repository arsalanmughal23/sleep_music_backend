<?php

namespace App\Repositories\Admin;

use App\Helper\Util;
use App\Models\Playlist;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PlaylistRepository
 * @package App\Repositories\Admin
 * @version December 28, 2018, 6:56 pm UTC
 *
 * @method Playlist findWithoutFail($id, $columns = ['*'])
 * @method Playlist find($id, $columns = ['*'])
 * @method Playlist first($columns = ['*'])
 */
class PlaylistRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'image',
        'type',
        'is_featured',
        'is_protected',
        'sort_key'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Playlist::class;
    }

    protected function handleFiles($input, $request)
    {
        if ($request->hasFile('image')) {
            $file           = $request->file('image');
            $input['image'] = \Storage::putFile('public/playlist_images', $file);
        }
        return $input;
    }

    /**
     * @param $request
     * @param bool $auth_user
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveRecord($request, $auth_user = false)
    {
        $params    = ['category_id', 'user_id', 'name', 'is_featured', 'type', 'category_id', 'parent_id', 'image'];
        $media_ids = isset($request->media_ids) ? $request->media_ids : [];
        $media_ids = is_array($media_ids) ? $media_ids : json_decode($media_ids);
        if ($auth_user) {
            $params = ['category_id', 'user_id', 'name', 'type', 'image'];
        }
        $input = $request->only($params);
        if ($auth_user) {
            $input['user_id'] = \Auth::id();
        } else {
            $input['is_protected'] = 1;
            $input['user_id']      = ($input['user_id'] == 0) ? null : $input['user_id'];
            $input['category_id']  = $request->get('category_id', -1) == -1 ? null : $input['category_id'];
            $input['parent_id']    = 0;
        }
        //$input = $this->handleFiles($input, $request);

        $playlist = $this->create($input);
        if ($media_ids && count($media_ids) > 0) {
            $playlist->media_all()->sync($media_ids);
        }
        return $playlist;
    }

    /**
     * @param $request
     * @param $playlist
     * @param bool $auth_user
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function updateRecord($request, $playlist, $auth_user = false)
    {
//        dd($playlist->id);
        $input = $request->only(['category_id', 'user_id', 'name', 'is_featured', 'type', 'category_id', 'parent_id', 'image']);
        if ($auth_user) {
            $input            = $request->only(['category_id', 'name', 'type', 'image']);
            $input['user_id'] = \Auth::id();
        } else {
            $input['is_protected'] = 1;
            $input['user_id']      = ($input['user_id'] == 0) ? null : $input['user_id'];
            $input['category_id']  = $request->get('category_id', -1) == -1 ? null : $input['category_id'];
            $input['parent_id']    = $request->get('parent_id', -1) == -1 ? null : $input['parent_id'];
        }
        //$input = $this->handleFiles($input, $request);

        $playlist = $this->update($input, $playlist->id);
        return $playlist;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $playlist = $this->delete($id);

        return $playlist;
    }

    public function syncMedia(Playlist $playlist, $request)
    {
        return $this->attachMedia($playlist, $request, true);
    }

    public function attachMedia(Playlist $playlist, $request, $detach = false)
    {
        $media_ids = $request->media;
        $media_ids = is_array($media_ids) ? $media_ids : json_decode($media_ids);
        foreach ($media_ids as $item) //  dd(gettype($item));
        {
            $item = (int)$item;
            //  dd(gettype($item));


            $ret         = false;
            $mediaBefore = count($playlist->media_all);
            $media       = explode(",", $item);
            // Remove all entries that are already attached.
            $mediaFound        = $playlist->media_all->pluck('id')->all();
            $removeMedia       = array_diff($mediaFound, $media);
            $removeMedia_count = count($removeMedia);
            $media             = array_diff($media, $mediaFound);
            $media_count       = count($media);
            if ($media_count > 0) {
                $playlist->media_all()->attach($media);
                $playlist->touch();
                $ret = true;
            }

            if ($detach && $removeMedia_count > 0) {
                $playlist->media_all()->detach($removeMedia);
                $playlist->touch();
                $ret = true;
            }

            $playlist = $this->findWithoutFail($playlist->id);

            // Newly attached media is atleast 1 and count of media before this attachment was less than 4;
            // Or (((count of media before this process) - (media to be removed)) + (Newly attached media)) is less than 4;
            if ($media_count > 0 && $mediaBefore < 4 || (($mediaBefore - $removeMedia_count) + $media_count) < 4) {
                $images = $playlist->media_all()->take(4)->pluck('image')->toArray();
                if (count($images) > 1) {
                    $collage = Util::makeCollage($images);
                    if ($collage !== false) {
                        $playlist->image = $collage;
                        $playlist->save();
                    }
                } else if (count($images) > 0) {
                    // Because Media can be deleted from admin.
                    $playlist->image = $images[0];
                    $playlist->save();
                }
            }

        }
        return $ret;
    }

    public function detachMedia($playlist, $request)
    {
        $media = explode(",", $request->get('media', ""));
        if (count($media) > 0) {
            $playlist->media_all()->detach($media);
            $playlist->touch();
            return true;
        }
        return false;
    }
}
