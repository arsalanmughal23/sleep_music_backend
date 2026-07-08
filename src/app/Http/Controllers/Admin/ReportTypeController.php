<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\ReportTypeDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateReportTypeRequest;
use App\Http\Requests\Admin\UpdateReportTypeRequest;
use App\Models\ReportType;
use App\Repositories\Admin\ReportTypeRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class ReportTypeController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  ReportTypeRepository */
    private $reportTypeRepository;

    public function __construct(ReportTypeRepository $reportTypeRepo)
    {
        $this->reportTypeRepository = $reportTypeRepo;
        $this->ModelName            = 'report-types';
        $this->BreadCrumbName       = 'Report Types';
    }

    /**
     * Display a listing of the ReportType.
     *
     * @param ReportTypeDataTable $reportTypeDataTable
     * @return Response
     */
    public function index(ReportTypeDataTable $reportTypeDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        return $reportTypeDataTable->render('admin.report_types.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new ReportType.
     *
     * @return Response
     */
    public function create()
    {
        $reportType = new ReportType();
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        return view('admin.report_types.create')->with(['reportType' => $reportType, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created ReportType in storage.
     *
     * @param CreateReportTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateReportTypeRequest $request)
    {
        $reportType = $this->reportTypeRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.report-types.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.report-types.edit', $reportType->id));
        } else {
            $redirect_to = redirect(route('admin.report-types.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified ReportType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $reportType = $this->reportTypeRepository->findWithoutFail($id);

        if (empty($reportType)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.report-types.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $reportType);
        return view('admin.report_types.show')->with(['reportType' => $reportType, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified ReportType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $reportType = $this->reportTypeRepository->findWithoutFail($id);

        if (empty($reportType)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.report-types.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $reportType);
        return view('admin.report_types.edit')->with(['reportType' => $reportType, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified ReportType in storage.
     *
     * @param  int $id
     * @param UpdateReportTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportTypeRequest $request)
    {
        $reportType = $this->reportTypeRepository->findWithoutFail($id);

        if (empty($reportType)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.report-types.index'));
        }

        $reportType = $this->reportTypeRepository->updateRecord($request, $reportType);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.report-types.create'));
        } else {
            $redirect_to = redirect(route('admin.report-types.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified ReportType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reportType = $this->reportTypeRepository->findWithoutFail($id);

        if (empty($reportType)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.report-types.index'));
        }

        $this->reportTypeRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.report-types.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
