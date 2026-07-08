<?php

namespace App\Repositories\Admin;

use App\Models\ClientConnectionLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ClientConnectionLogRepository
 * @package App\Repositories\Admin
 * @version August 29, 2019, 10:35 am UTC
 *
 * @method ClientConnectionLog findWithoutFail($id, $columns = ['*'])
 * @method ClientConnectionLog find($id, $columns = ['*'])
 * @method ClientConnectionLog first($columns = ['*'])
 */
class ClientConnectionLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'client_id',
        'status',
        'seconds_until_next'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ClientConnectionLog::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input               = $request->all();
        $clientConnectionLog = $this->create($input);
        return $clientConnectionLog;
    }

    /**
     * @param $request
     * @param $clientConnectionLog
     * @return mixed
     */
    public function updateRecord($request, $clientConnectionLog)
    {
        $input               = $request->all();
        $clientConnectionLog = $this->update($input, $clientConnectionLog->id);
        return $clientConnectionLog;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $clientConnectionLog = $this->delete($id);
        return $clientConnectionLog;
    }
}
