<?php

namespace App\Repositories\Admin;

use App\Models\Transaction;
use Illuminate\Http\Request;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TransactionRepository
 * @package App\Repositories\Admin
 * @version December 15, 2021, 7:13 pm UTC
 *
 * @method Transaction findWithoutFail($id, $columns = ['*'])
 * @method Transaction find($id, $columns = ['*'])
 * @method Transaction first($columns = ['*'])
 */
class TransactionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'order_id',
        'currency',
        'amount',
        'status',
        'created_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Transaction::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input       = $request instanceof Request ? $request->all() : $request;
        $transaction = $this->create($input);
        return $transaction;
    }

    /**
     * @param $request
     * @param $transaction
     * @return mixed
     */
    public function updateRecord($request, $transaction)
    {
        $input       = $request->all();
        $transaction = $this->update($input, $transaction->id);
        return $transaction;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $transaction = $this->delete($id);
        return $transaction;
    }
}
