<?php

namespace App\Repositories\Admin;

use App\Models\Client;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ClientRepository
 * @package App\Repositories\Admin
 * @version June 14, 2019, 12:39 pm UTC
 *
 * @method Client findWithoutFail($id, $columns = ['*'])
 * @method Client find($id, $columns = ['*'])
 * @method Client first($columns = ['*'])
 */
class ClientRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name',
        'mac',
        'connection_limit',
        'cidr',
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Client::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input        = $request->all();
        $input['mac'] = str_replace(":", "-", $request->get('mac'));
        $client       = $this->create($input);
        return $client;
    }

    /**
     * @param $request
     * @param $client
     * @return mixed
     */
    public function updateRecord($request, $client)
    {
        $input        = $request->all();
        $input['mac'] = str_replace(":", "-", $request->get('mac'));
        $client       = $this->update($input, $client->id);
        return $client;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $client = $this->delete($id);
        return $client;
    }
}
