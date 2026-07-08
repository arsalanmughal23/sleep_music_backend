<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\AnalyticDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateAnalyticRequest;
use App\Http\Requests\Admin\UpdateAnalyticRequest;
use App\Repositories\Admin\AnalyticRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class AnalyticController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  AnalyticRepository */
    private $analyticRepository;

    public function __construct(AnalyticRepository $analyticRepo)
    {
        $this->analyticRepository = $analyticRepo;
        $this->ModelName = 'analytics';
        $this->BreadCrumbName = 'Analytics';
    }

    /**
     * Display a listing of the Analytic.
     *
     * @param AnalyticDataTable $analyticDataTable
     * @return Response
     */
    public function index(AnalyticDataTable $analyticDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $analyticDataTable->render('admin.analytics.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Analytic.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.analytics.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Analytic in storage.
     *
     * @param CreateAnalyticRequest $request
     *
     * @return Response
     */
    public function store(CreateAnalyticRequest $request)
    {
        $analytic = $this->analyticRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.analytics.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.analytics.edit', $analytic->id));
        } else {
            $redirect_to = redirect(route('admin.analytics.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Analytic.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $analytic = $this->analyticRepository->findWithoutFail($id);

        if (empty($analytic)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.analytics.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $analytic);
        return view('admin.analytics.show')->with(['analytic' => $analytic, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Analytic.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $analytic = $this->analyticRepository->findWithoutFail($id);

        if (empty($analytic)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.analytics.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $analytic);
        return view('admin.analytics.edit')->with(['analytic' => $analytic, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Analytic in storage.
     *
     * @param  int              $id
     * @param UpdateAnalyticRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAnalyticRequest $request)
    {
        $analytic = $this->analyticRepository->findWithoutFail($id);

        if (empty($analytic)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.analytics.index'));
        }

        $analytic = $this->analyticRepository->updateRecord($request, $analytic);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.analytics.create'));
        } else {
            $redirect_to = redirect(route('admin.analytics.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Analytic from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $analytic = $this->analyticRepository->findWithoutFail($id);

        if (empty($analytic)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.analytics.index'));
        }

        $this->analyticRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.analytics.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
