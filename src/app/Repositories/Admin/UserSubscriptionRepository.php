<?php

namespace App\Repositories\Admin;

use App\Models\UserSubscription;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UserSubscriptionRepository
 * @package App\Repositories\Admin
 * @version August 21, 2023, 5:12 pm UTC
 *
 * @method UserSubscription findWithoutFail($id, $columns = ['*'])
 * @method UserSubscription find($id, $columns = ['*'])
 * @method UserSubscription first($columns = ['*'])
*/
class UserSubscriptionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'reference_key',
        'platform',
        'data',
        'expiry_date',
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UserSubscription::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($input)
    {
//        $input = $request->all();
        $userSubscription = $this->create($input);
        return $userSubscription;
    }

    /**
     * @param $request
     * @param $userSubscription
     * @return mixed
     */
    public function updateRecord($request, $userSubscription)
    {
        $input = $request->all();
        $userSubscription = $this->update($input, $userSubscription->id);
        return $userSubscription;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $userSubscription = $this->delete($id);
        return $userSubscription;
    }
}
