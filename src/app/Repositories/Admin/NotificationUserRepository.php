<?php

namespace App\Repositories\Admin;

use App\Models\NotificationUser;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class NotificationUserRepository
 * @package App\Repositories\Admin
 * @version November 9, 2021, 5:26 pm UTC
 *
 * @method NotificationUser findWithoutFail($id, $columns = ['*'])
 * @method NotificationUser find($id, $columns = ['*'])
 * @method NotificationUser first($columns = ['*'])
*/
class NotificationUserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'notification_id',
        'user_id',
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NotificationUser::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input = $request->all();
        $notificationUser = $this->create($input);
        return $notificationUser;
    }

    /**
     * @param $request
     * @param $notificationUser
     * @return mixed
     */
    public function updateRecord($request, $notificationUser)
    {
        $input = $request->all();
        $notificationUser = $this->update($input, $notificationUser->id);
        return $notificationUser;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $notificationUser = $this->delete($id);
        return $notificationUser;
    }
}
