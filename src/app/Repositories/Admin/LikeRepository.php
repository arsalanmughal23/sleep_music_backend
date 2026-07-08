<?php

namespace App\Repositories\Admin;

use App\Models\Like;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LikeRepository
 * @package App\Repositories\Admin
 * @version September 21, 2021, 2:50 pm UTC
 *
 * @method Like findWithoutFail($id, $columns = ['*'])
 * @method Like find($id, $columns = ['*'])
 * @method Like first($columns = ['*'])
 */
class LikeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'media_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Like::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input            = $request->all();
        $input['user_id'] = \Auth::id();
        $like             = $this->updateOrCreate([
            'user_id'  => \Auth::id(),
            'media_id' => $input['media_id']
        ], $input);
        return $like;
    }

    /**
     * @param $request
     * @param $like
     * @return mixed
     */
    public function updateRecord($request, $like)
    {
        $input = $request->all();
        $like  = $this->update($input, $like->id);
        return $like;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $like = $this->delete($id);
        return $like;
    }
}
