<?php

namespace App\Repositories\Admin;

use App\Models\View;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ViewRepository
 * @package App\Repositories\Admin
 * @version October 26, 2021, 2:36 pm UTC
 *
 * @method View findWithoutFail($id, $columns = ['*'])
 * @method View find($id, $columns = ['*'])
 * @method View first($columns = ['*'])
 */
class ViewRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'user_id',
        'media_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return View::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input            = $request->all();
        $input['user_id'] = \Auth::id();
//        $input['user_id'] = 37507;
        $view = $this->updateOrCreate($input);
        return $view;
    }

    /**
     * @param $request
     * @param $view
     * @return mixed
     */
    public function updateRecord($request, $view)
    {
        $input = $request->all();
        $view  = $this->updateOrCreate($input, $view->id);
        return $view;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $view = $this->delete($id);
        return $view;
    }
}
