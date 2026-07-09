<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\ViewDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateViewRequest;
use App\Http\Requests\Admin\UpdateViewRequest;
use App\Repositories\Admin\ViewRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class ViewController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  ViewRepository */
    private $viewRepository;

    public function __construct(ViewRepository $viewRepo)
    {
        $this->viewRepository = $viewRepo;
        $this->ModelName = 'views';
        $this->BreadCrumbName = 'Views';
    }

    /**
     * Display a listing of the View.
     *
     * @param ViewDataTable $viewDataTable
     * @return Response
     */
    public function index(ViewDataTable $viewDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $viewDataTable->render('admin.views.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new View.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.views.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created View in storage.
     *
     * @param CreateViewRequest $request
     *
     * @return Response
     */
    public function store(CreateViewRequest $request)
    {
        $view = $this->viewRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.views.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.views.edit', $view->id));
        } else {
            $redirect_to = redirect(route('admin.views.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified View.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $view = $this->viewRepository->findWithoutFail($id);

        if (empty($view)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.views.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $view);
        return view('admin.views.show')->with(['view' => $view, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified View.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $view = $this->viewRepository->findWithoutFail($id);

        if (empty($view)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.views.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $view);
        return view('admin.views.edit')->with(['view' => $view, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified View in storage.
     *
     * @param  int              $id
     * @param UpdateViewRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateViewRequest $request)
    {
        $view = $this->viewRepository->findWithoutFail($id);

        if (empty($view)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.views.index'));
        }

        $view = $this->viewRepository->updateRecord($request, $view);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.views.create'));
        } else {
            $redirect_to = redirect(route('admin.views.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified View from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $view = $this->viewRepository->findWithoutFail($id);

        if (empty($view)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.views.index'));
        }

        $this->viewRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.views.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
