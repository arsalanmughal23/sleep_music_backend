<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\TransactionDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateTransactionRequest;
use App\Http\Requests\Admin\UpdateTransactionRequest;
use App\Repositories\Admin\TransactionRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class TransactionController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  TransactionRepository */
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepo)
    {
        $this->transactionRepository = $transactionRepo;
        $this->ModelName = 'transactions';
        $this->BreadCrumbName = 'Transactions';
    }

    /**
     * Display a listing of the Transaction.
     *
     * @param TransactionDataTable $transactionDataTable
     * @return Response
     */
    public function index(TransactionDataTable $transactionDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $transactionDataTable->render('admin.transactions.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Transaction.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.transactions.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Transaction in storage.
     *
     * @param CreateTransactionRequest $request
     *
     * @return Response
     */
    public function store(CreateTransactionRequest $request)
    {
        $transaction = $this->transactionRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.transactions.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.transactions.edit', $transaction->id));
        } else {
            $redirect_to = redirect(route('admin.transactions.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Transaction.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $transaction = $this->transactionRepository->findWithoutFail($id);

        if (empty($transaction)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.transactions.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $transaction);
        return view('admin.transactions.show')->with(['transaction' => $transaction, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Transaction.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $transaction = $this->transactionRepository->findWithoutFail($id);

        if (empty($transaction)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.transactions.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $transaction);
        return view('admin.transactions.edit')->with(['transaction' => $transaction, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Transaction in storage.
     *
     * @param  int              $id
     * @param UpdateTransactionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTransactionRequest $request)
    {
        $transaction = $this->transactionRepository->findWithoutFail($id);

        if (empty($transaction)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.transactions.index'));
        }

        $transaction = $this->transactionRepository->updateRecord($request, $transaction);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.transactions.create'));
        } else {
            $redirect_to = redirect(route('admin.transactions.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Transaction from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $transaction = $this->transactionRepository->findWithoutFail($id);

        if (empty($transaction)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.transactions.index'));
        }

        $this->transactionRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.transactions.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
