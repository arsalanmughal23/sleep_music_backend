<?php

namespace App\Repositories\Admin;

use App\Models\Category;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Common\BaseRepository;
use Auth;

/**
 * Class MediaRepository
 * @package App\Repositories\Admin
 * @version December 28, 2018, 6:54 pm UTC
 *
 * @method Media findWithoutFail($id, $columns = ['*'])
 * @method Media find($id, $columns = ['*'])
 * @method Media first($columns = ['*'])
 */
class MediaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'is_premium',
        'user_id',
        'category_id',
        'name',
        'is_featured',
        'image'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Media::class;
    }

    private function handleFiles($input, $request)
    {
        if ($request->hasFile('file')) {
            $file               = $request->file('file');
            $input['file_mime'] = $file->getMimeType();
            $input['file_type'] = Category::TYPE_VIDEO;
            if (strpos($file->getMimeType(), "audio/") !== false) {
                $input['file_type'] = Category::TYPE_AUDIO;
                $input['file_path'] = Storage::putFileAs('public/media_files', $file, str_random(40) . '.' . $file->getClientOriginalExtension());
            } else {
                $input['file_path'] = Storage::putFile('public/media_files', $file);
            }
//            $input['file_url'] = "/public" . Storage::url($input['file_path']);
            $input['file_url'] = Storage::url($input['file_path']);
        }
        if ($request->hasFile('image')) {
            $file           = $request->file('image');
            $input['image'] = Storage::putFile('public/media_images', $file);
        }

        return $input;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($input)
    {
        $input['file_type'] = Category::TYPE_AUDIO;
        
        if (empty($input['user_id'])) {
            $input['user_id'] = Auth::id();
        }
        $media = $this->create($input);
        return $media;
    }

    /**
     * @param $request
     * @param $media
     * @return mixed
     */
    public function updateRecord($input, $id)
    {
        $input['file_type'] = Category::TYPE_AUDIO;
        $media = $this->update($input, $id);
        return $media;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $media = $this->delete($id);
        return $media;
    }

    public function syncPlaylist($model, $request)
    {
        $playlist = $request;
        if ($request instanceof Request) {
            $playlist = $request->get('playlist');
        }
        return $model->playlist()->sync($playlist);
    }
}
