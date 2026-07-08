<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\ClientConnectionLogDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateClientConnectionLogRequest;
use App\Http\Requests\Admin\UpdateClientConnectionLogRequest;
use App\Repositories\Admin\ClientConnectionLogRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class ClientConnectionLogController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  ClientConnectionLogRepository */
    private $clientConnectionLogRepository;

    public function __construct(ClientConnectionLogRepository $clientConnectionLogRepo)
    {
        $this->clientConnectionLogRepository = $clientConnectionLogRepo;
        $this->ModelName                     = 'client-connection-logs';
        $this->BreadCrumbName                = 'Client Connection Logs';
    }

    /**
     * Display a listing of the ClientConnectionLog.
     *
     * @param ClientConnectionLogDataTable $clientConnectionLogDataTable
     * @return Response
     */
    public function index(ClientConnectionLogDataTable $clientConnectionLogDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        return $clientConnectionLogDataTable->render('admin.client_connection_logs.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new ClientConnectionLog.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        return view('admin.client_connection_logs.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created ClientConnectionLog in storage.
     *
     * @param CreateClientConnectionLogRequest $request
     *
     * @return Response
     */
    public function store(CreateClientConnectionLogRequest $request)
    {
        $clientConnectionLog = $this->clientConnectionLogRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.client-connection-logs.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.client-connection-logs.edit', $clientConnectionLog->id));
        } else {
            $redirect_to = redirect(route('admin.client-connection-logs.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified ClientConnectionLog.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $clientConnectionLog = $this->clientConnectionLogRepository->findWithoutFail($id);

        if (empty($clientConnectionLog)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.client-connection-logs.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $clientConnectionLog);
        return view('admin.client_connection_logs.show')->with(['clientConnectionLog' => $clientConnectionLog, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified ClientConnectionLog.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $clientConnectionLog = $this->clientConnectionLogRepository->findWithoutFail($id);

        if (empty($clientConnectionLog)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.client-connection-logs.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $clientConnectionLog);
        return view('admin.client_connection_logs.edit')->with(['clientConnectionLog' => $clientConnectionLog, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified ClientConnectionLog in storage.
     *
     * @param  int $id
     * @param UpdateClientConnectionLogRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateClientConnectionLogRequest $request)
    {
        $clientConnectionLog = $this->clientConnectionLogRepository->findWithoutFail($id);

        if (empty($clientConnectionLog)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.client-connection-logs.index'));
        }

        $clientConnectionLog = $this->clientConnectionLogRepository->updateRecord($request, $clientConnectionLog);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.client-connection-logs.create'));
        } else {
            $redirect_to = redirect(route('admin.client-connection-logs.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified ClientConnectionLog from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $clientConnectionLog = $this->clientConnectionLogRepository->findWithoutFail($id);

        if (empty($clientConnectionLog)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.client-connection-logs.index'));
        }

        $this->clientConnectionLogRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.client-connection-logs.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
