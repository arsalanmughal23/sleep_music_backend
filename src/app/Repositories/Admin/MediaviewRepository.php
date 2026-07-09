<?php

namespace App\Repositories\Admin;

use App\Models\Mediaview;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MediaviewRepository
 * @package App\Repositories\Admin
 * @version December 6, 2021, 1:01 pm UTC
 *
 * @method Mediaview findWithoutFail($id, $columns = ['*'])
 * @method Mediaview find($id, $columns = ['*'])
 * @method Mediaview first($columns = ['*'])
 */
class MediaviewRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'media_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Mediaview::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($id)
    {
        $input['user_id']  = \Auth::id();
        $input['media_id'] = $id;
        $mediaview         = $this->updateOrCreate($input);
        return $mediaview;
    }

    /**
     * @param $request
     * @param $mediaview
     * @return mixed
     */
    public function updateRecord($request, $mediaview)
    {
        $input     = $request->all();
        $mediaview = $this->update($input, $mediaview->id);
        return $mediaview;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $mediaview = $this->delete($id);
        return $mediaview;
    }
}
