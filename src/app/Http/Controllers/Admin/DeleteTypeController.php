<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\DeleteTypeDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateDeleteTypeRequest;
use App\Http\Requests\Admin\UpdateDeleteTypeRequest;
use App\Models\DeleteType;
use App\Repositories\Admin\DeleteTypeRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class DeleteTypeController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  DeleteTypeRepository */
    private $deleteTypeRepository;

    public function __construct(DeleteTypeRepository $deleteTypeRepo)
    {
        $this->deleteTypeRepository = $deleteTypeRepo;
        $this->ModelName = 'delete-types';
        $this->BreadCrumbName = 'Delete Types';
    }

    /**
     * Display a listing of the DeleteType.
     *
     * @param DeleteTypeDataTable $deleteTypeDataTable
     * @return Response
     */
    public function index(DeleteTypeDataTable $deleteTypeDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $deleteTypeDataTable->render('admin.delete_types.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new DeleteType.
     *
     * @return Response
     */
    public function create()
    {
        $deleteType = new DeleteType();
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.delete_types.create')->with(['deleteType' => $deleteType, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created DeleteType in storage.
     *
     * @param CreateDeleteTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateDeleteTypeRequest $request)
    {
        $deleteType = $this->deleteTypeRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.delete-types.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.delete-types.edit', $deleteType->id));
        } else {
            $redirect_to = redirect(route('admin.delete-types.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified DeleteType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $deleteType = $this->deleteTypeRepository->findWithoutFail($id);

        if (empty($deleteType)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.delete-types.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $deleteType);
        return view('admin.delete_types.show')->with(['deleteType' => $deleteType, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified DeleteType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $deleteType = $this->deleteTypeRepository->findWithoutFail($id);

        if (empty($deleteType)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.delete-types.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $deleteType);
        return view('admin.delete_types.edit')->with(['deleteType' => $deleteType, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified DeleteType in storage.
     *
     * @param  int              $id
     * @param UpdateDeleteTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDeleteTypeRequest $request)
    {
        $deleteType = $this->deleteTypeRepository->findWithoutFail($id);

        if (empty($deleteType)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.delete-types.index'));
        }

        $deleteType = $this->deleteTypeRepository->updateRecord($request, $deleteType);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.delete-types.create'));
        } else {
            $redirect_to = redirect(route('admin.delete-types.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified DeleteType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $deleteType = $this->deleteTypeRepository->findWithoutFail($id);

        if (empty($deleteType)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.delete-types.index'));
        }

        $this->deleteTypeRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.delete-types.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
