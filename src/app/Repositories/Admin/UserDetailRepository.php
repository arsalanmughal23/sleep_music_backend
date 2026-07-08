<?php

namespace App\Repositories\Admin;

use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UserDetailRepository
 * @package App\Repositories\Admin
 * @version April 2, 2018, 9:11 am UTC
 *
 * @method UserDetail findWithoutFail($id, $columns = ['*'])
 * @method UserDetail find($id, $columns = ['*'])
 * @method UserDetail first($columns = ['*'])
 */
class UserDetailRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserDetail::class;
    }

    /**
     * @param $id
     * @param $request
     * @return mixed
     */
    public function saveRecord($id, $request)
    {
        $userDetailData             = $request->only(['first_name', 'last_name', 'image', 'is_verified']);
        $userDetails['user_id']     = $id;
        $userDetails['first_name']  = ucwords($userDetailData['first_name']);
        $userDetails['last_name']   = ucwords($userDetailData['last_name']);
        $userDetails['image']       = isset($userDetailData['image']) ? $userDetailData['image'] : null;
        $userDetails['is_verified'] = $userDetailData['is_verified'] ?? false;

        $userDetails = $this->create($userDetails);
        return $userDetails;
    }

    /**
     * @param $id
     * @param $request
     * @return mixed
     */
    public function updateRecord($id, $request)
    {
        $updateData  = [];
        $userDetails = $this->findWhere(['user_id' => $id])->first();
        if ($userDetails) {
            if ($request->first_name) {
                $updateData['first_name'] = $request->get('first_name');
            }

            if ($request->last_name) {
                $updateData['last_name'] = $request->get('last_name');
            }

            if($request->image){
                $updateData['image'] = $request->image;
            }

            $userDetails = $userDetails->update($updateData);
        }
        return $userDetails;
    }
}