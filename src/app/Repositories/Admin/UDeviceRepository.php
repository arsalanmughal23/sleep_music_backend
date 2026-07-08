<?php

namespace App\Repositories\Admin;

use App\Models\UserDevice;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UDeviceRepository
 * @package App\Repositories\Admin
 * @version July 14, 2018, 9:11 am UTC
 *
 * @method UserDevice findWithoutFail($id, $columns = ['*'])
 * @method UserDevice find($id, $columns = ['*'])
 * @method UserDevice first($columns = ['*'])
 */
class UDeviceRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return UserDevice::class;
    }

    /**
     * @param $id
     * @param $request
     */
    public function saveRecord($id, $request)
    {
        $userDeviceData = $request->only(['device_token', 'device_type', 'push_notification']);
        // Detach All users with this device_token so that old user of this device does not get notification of the old user.
        $tokenDevice = $this->model->where('device_token', $userDeviceData['device_token']);
        if ($tokenDevice->exists()) {
            $tokenDevice->delete();
        }
        $userDeviceData['user_id'] = $id;
        $userDeviceData['push_notification'] = $userDeviceData['push_notification'] ?? 1;
        $this->model->updateOrCreate(['user_id' => $id], $userDeviceData);
    }
}
