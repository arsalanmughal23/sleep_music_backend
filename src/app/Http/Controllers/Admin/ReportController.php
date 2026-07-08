<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\ReportDataTable;
use App\Http\Requests\Admin\CreateReportRequest;
use App\Http\Requests\Admin\UpdateReportRequest;
use App\Repositories\Admin\ReportRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Admin\UpdateStatusReportRequest;
use App\Models\Report;
use App\Models\ReportType;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReportController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  ReportRepository */
    private $reportRepository;

    public function __construct(ReportRepository $reportRepo)
    {
        $this->reportRepository = $reportRepo;
        $this->ModelName = 'reports';
        $this->BreadCrumbName = 'Reports';
    }

    /**
     * Display a listing of the Report.
     *
     * @param ReportDataTable $reportDataTable
     * @return Response
     */
    public function index(ReportDataTable $reportDataTable, Request $request)
    {
        $reportTypes = ReportType::all();
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        $reportDataTable->report_type_id = $request->get('report_type_id', null);
        $reportDataTable->status = $request->get('status', null);

        return $reportDataTable->render('admin.reports.index', ['title' => $this->BreadCrumbName, 'reportTypes' => $reportTypes]);
    }

    /**
     * Show the form for creating a new Report.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.reports.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Report in storage.
     *
     * @param CreateReportRequest $request
     *
     * @return Response
     */
    public function store(CreateReportRequest $request)
    {
        $report = $this->reportRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.reports.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.reports.edit', $report->id));
        } else {
            $redirect_to = redirect(route('admin.reports.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Report.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $report = $this->reportRepository->findWithoutFail($id);

        if (empty($report)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.reports.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $report);
        return view('admin.reports.show')->with(['report' => $report, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Report.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $report = $this->reportRepository->findWithoutFail($id);

        if (empty($report)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.reports.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $report);
        return view('admin.reports.edit')->with(['report' => $report, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Report in storage.
     *
     * @param  int              $id
     * @param UpdateReportRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateReportRequest $request)
    {
        $report = $this->reportRepository->findWithoutFail($id);

        if (empty($report)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.reports.index'));
        }

        $report = $this->reportRepository->updateRecord($request, $report);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.reports.create'));
        } else {
            $redirect_to = redirect(route('admin.reports.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    public function status($id, UpdateStatusReportRequest $request)
    {
        try{
            $report = Report::find($id);

            if (!$report) {
                Flash::error($this->BreadCrumbName . ' not found');
                return redirect(route('admin.reports.index'));
            }
            
            $statusUpdated = DB::table('report')->whereId($id)->update(['status' => $request->status]);

            if($statusUpdated){
                return redirect()->back()->with(['message' => $this->BreadCrumbName . ' status updated successfully.']);
            }else{
                throw new \Error('Something went wrong!');
            }

        } catch (\Error $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }
    

    /**
     * Remove the specified Report from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $report = $this->reportRepository->findWithoutFail($id);

        if (empty($report)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.reports.index'));
        }

        $this->reportRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.reports.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
