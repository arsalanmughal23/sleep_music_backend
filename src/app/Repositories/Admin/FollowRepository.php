<?php

namespace App\Repositories\Admin;

use App\Models\Follow;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FollowRepository
 * @package App\Repositories\Admin
 * @version September 14, 2021, 9:18 pm UTC
 *
 * @method Follow findWithoutFail($id, $columns = ['*'])
 * @method Follow find($id, $columns = ['*'])
 * @method Follow first($columns = ['*'])
 */
class FollowRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'followed_user_id',
        'followed_by_user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Follow::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input                        = $request->all();
        $input['followed_by_user_id'] = \Auth::id();
        $follow                       = $this->create($input);
        return $follow;
    }

    /**
     * @param $request
     * @param $follow
     * @return mixed
     */
    public function updateRecord($request, $follow)
    {
        $input  = $request->all();
        $follow = $this->update($input, $follow->id);
        return $follow;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $follow = $this->delete($id);
        return $follow;
    }
}
