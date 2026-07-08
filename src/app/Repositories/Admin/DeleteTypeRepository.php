<?php

namespace App\Repositories\Admin;

use App\Models\DeleteType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DeleteTypeRepository
 * @package App\Repositories\Admin
 * @version August 16, 2023, 5:07 pm UTC
 *
 * @method DeleteType findWithoutFail($id, $columns = ['*'])
 * @method DeleteType find($id, $columns = ['*'])
 * @method DeleteType first($columns = ['*'])
*/
class DeleteTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name',
        'status',
        'created_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DeleteType::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input = $request->all();
        $deleteType = $this->create($input);
        return $deleteType;
    }

    /**
     * @param $request
     * @param $deleteType
     * @return mixed
     */
    public function updateRecord($request, $deleteType)
    {
        $input = $request->all();
        $deleteType = $this->update($input, $deleteType->id);
        return $deleteType;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $deleteType = $this->delete($id);
        return $deleteType;
    }
}
