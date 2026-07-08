<?php

namespace App\Repositories\Admin;

use App\Models\Package;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PackageRepository
 * @package App\Repositories\Admin
 * @version August 21, 2023, 5:51 pm UTC
 *
 * @method Package findWithoutFail($id, $columns = ['*'])
 * @method Package find($id, $columns = ['*'])
 * @method Package first($columns = ['*'])
*/
class PackageRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name',
        'price',
        'currency',
        'status',
        'is_default',
        'created_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Package::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input = $request->all();
        $package = $this->create($input);
        return $package;
    }

    /**
     * @param $request
     * @param $package
     * @return mixed
     */
    public function updateRecord($request, $package)
    {
        $input = $request->all();
        $package = $this->update($input, $package->id);
        return $package;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $package = $this->delete($id);
        return $package;
    }
}
