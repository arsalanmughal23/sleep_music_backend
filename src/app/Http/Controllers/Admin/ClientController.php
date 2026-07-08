<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ClientConnectionLogDataTable;
use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\ClientDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Repositories\Admin\ClientRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class ClientController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  ClientRepository */
    private $clientRepository;

    public function __construct(ClientRepository $clientRepo)
    {
        $this->clientRepository = $clientRepo;
        $this->ModelName        = 'clients';
        $this->BreadCrumbName   = 'Clients';
    }

    /**
     * Display a listing of the Client.
     *
     * @param ClientDataTable $clientDataTable
     * @return Response
     */
    public function index(ClientDataTable $clientDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        return $clientDataTable->render('admin.clients.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Client.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        return view('admin.clients.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Client in storage.
     *
     * @param CreateClientRequest $request
     *
     * @return Response
     */
    public function store(CreateClientRequest $request)
    {
        $client = $this->clientRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.clients.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.clients.edit', $client->id));
        } else {
            $redirect_to = redirect(route('admin.clients.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Client.
     *
     * @param  int $id
     *
     * @param ClientConnectionLogDataTable $clientConnectionLogDataTable
     * @return Response
     */
    public function show($id, ClientConnectionLogDataTable $clientConnectionLogDataTable)
    {
        $client                                  = $this->clientRepository->findWithoutFail($id);
        $clientConnectionLogDataTable->client_id = $id;

        if (empty($client)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.clients.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $client);
        return $clientConnectionLogDataTable->render('admin.clients.show', [
            'client' => $client,
            'title'  => $this->BreadCrumbName
        ]);
    }

    /**
     * Show the form for editing the specified Client.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $client = $this->clientRepository->findWithoutFail($id);

        if (empty($client)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.clients.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $client);
        return view('admin.clients.edit')->with(['client' => $client, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Client in storage.
     *
     * @param  int $id
     * @param UpdateClientRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateClientRequest $request)
    {
        $client = $this->clientRepository->findWithoutFail($id);

        if (empty($client)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.clients.index'));
        }

        $client = $this->clientRepository->updateRecord($request, $client);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.clients.create'));
        } else {
            $redirect_to = redirect(route('admin.clients.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Client from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $client = $this->clientRepository->findWithoutFail($id);

        if (empty($client)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.clients.index'));
        }

        $this->clientRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.clients.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
