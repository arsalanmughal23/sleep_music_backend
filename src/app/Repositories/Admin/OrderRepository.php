<?php

namespace App\Repositories\Admin;

use App\Models\Order;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class OrderRepository
 * @package App\Repositories\Admin
 * @version December 15, 2021, 7:12 pm UTC
 *
 * @method Order findWithoutFail($id, $columns = ['*'])
 * @method Order find($id, $columns = ['*'])
 * @method Order first($columns = ['*'])
 */
class OrderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'status',
        'total_amount',
        'datetime',
        'donated_to_id',
        'created_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Order::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        if (isset($request->nonce)) {
            $input = $request->only(['total_amount', 'datetime', 'donated_to_id']);

        } else {
            $input = $request->only(['total_amount', 'card_id', 'datetime', 'donated_to_id']);
        }

        $input['user_id'] = \Auth::id();
        $order            = $this->create($input);
        return $order;
    }

    /**
     * @param $request
     * @param $order
     * @return mixed
     */
    public function updateRecord($request, $order)
    {
        $input = $request->all();
        $order = $this->update($input, $order->id);
        return $order;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $order = $this->delete($id);
        return $order;
    }
}
