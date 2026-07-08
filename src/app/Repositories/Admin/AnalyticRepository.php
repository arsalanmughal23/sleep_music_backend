<?php

namespace App\Repositories\Admin;

use App\Models\Analytic;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AnalyticRepository
 * @package App\Repositories\Admin
 * @version September 20, 2021, 11:13 am UTC
 *
 * @method Analytic findWithoutFail($id, $columns = ['*'])
 * @method Analytic find($id, $columns = ['*'])
 * @method Analytic first($columns = ['*'])
*/
class AnalyticRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'music_id',
        'views',
        'user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Analytic::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input = $request->all();
        $analytic = $this->create($input);
        return $analytic;
    }

    /**
     * @param $request
     * @param $analytic
     * @return mixed
     */
    public function updateRecord($request, $analytic)
    {
        $input = $request->all();
        $analytic = $this->update($input, $analytic->id);
        return $analytic;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $analytic = $this->delete($id);
        return $analytic;
    }
}
