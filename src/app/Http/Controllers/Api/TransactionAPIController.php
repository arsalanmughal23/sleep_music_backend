<?php

namespace App\Http\Controllers\Api;

use App\Criteria\TransactionCriteria;
use App\Http\Requests\Api\CreateTransactionAPIRequest;
use App\Http\Requests\Api\UpdateTransactionAPIRequest;
use App\Models\Transaction;
use App\Repositories\Admin\TransactionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class TransactionController
 * @package App\Http\Controllers\Api
 */
class TransactionAPIController extends AppBaseController
{
    /** @var  TransactionRepository */
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepo)
    {
        $this->transactionRepository = $transactionRepo;
    }

    public function index(Request $request)
    {
        $limit = $request->get('limit', \config('constants.limit'));
        $this->transactionRepository->pushCriteria(new RequestCriteria($request));
        $this->transactionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->transactionRepository->pushCriteria(new TransactionCriteria([
            'user_id' => $request->user_id ?? \Auth::id(),
            'status' => $request->status ?? 'succeeded'
        ]));
        $transactions = $this->transactionRepository->paginate($limit);

        return $this->sendResponse($transactions->toArray(), 'Transactions retrieved successfully');
    }

    public function store(CreateTransactionAPIRequest $request)
    {
        $transactions = $this->transactionRepository->saveRecord($request);

        return $this->sendResponse($transactions->toArray(), 'Transaction saved successfully');
    }

    public function show($id)
    {
        /** @var Transaction $transaction */
        $transaction = $this->transactionRepository->findWithoutFail($id);

        if (empty($transaction)) {
            return $this->sendError('Transaction not found');
        }

        return $this->sendResponse($transaction->toArray(), 'Transaction retrieved successfully');
    }

    public function update($id, UpdateTransactionAPIRequest $request)
    {
        /** @var Transaction $transaction */
        $transaction = $this->transactionRepository->findWithoutFail($id);

        if (empty($transaction)) {
            return $this->sendError('Transaction not found');
        }

        $transaction = $this->transactionRepository->updateRecord($request, $id);

        return $this->sendResponse($transaction->toArray(), 'Transaction updated successfully');
    }

    public function destroy($id)
    {
        /** @var Transaction $transaction */
        $transaction = $this->transactionRepository->findWithoutFail($id);

        if (empty($transaction)) {
            return $this->sendError('Transaction not found');
        }

        $this->transactionRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Transaction deleted successfully');
    }

    public function getreceived()
    {

    }
}
