<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateReportAPIRequest;
use App\Http\Requests\Api\UpdateReportAPIRequest;
use App\Models\Report;
use App\Repositories\Admin\ReportRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class ReportController
 * @package App\Http\Controllers\Api
 */
class ReportAPIController extends AppBaseController
{
    /** @var  ReportRepository */
    private $reportRepository;

    public function __construct(ReportRepository $reportRepo)
    {
        $this->reportRepository = $reportRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/reports",
     *      summary="Get a listing of the Reports.",
     *      tags={"Report"},
     *      description="Get all Reports",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          description="Change the Default Record Count. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *     @SWG\Parameter(
     *          name="offset",
     *          description="Change the Default Offset of the Query. If not found, 0 will be used.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Report")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->reportRepository->pushCriteria(new RequestCriteria($request));
        $this->reportRepository->pushCriteria(new LimitOffsetCriteria($request));
        $reports = $this->reportRepository->all();
        return $this->sendResponse($reports->toArray(), 'Reports retrieved successfully');
    }

    /**
     * @param CreateReportAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reports",
     *      summary="Store a newly created Report in storage",
     *      tags={"Report"},
     *      description="Store Report",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Report that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Report")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Report"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportAPIRequest $request)
    {
        $report = $this->reportRepository->saveRecord($request);
        $report = $this->reportRepository->findWithoutFail($report->id);

        //  DB::statement("INSERT INTO notifications_admin (message) VALUES ('User/Media Reported $reports->user_id -$reports->media_id)'");
        return $this->sendResponse($report->toArray(), 'Report saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reports/{id}",
     *      summary="Display the specified Report",
     *      tags={"Report"},
     *      description="Get Report",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Report",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Report"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var Report $report */
        $report = $this->reportRepository->findWithoutFail($id);

        if (empty($report)) {
            return $this->sendErrorWithData(['Report not found']);
        }

        return $this->sendResponse($report->toArray(), 'Report retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateReportAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/reports/{id}",
     *      summary="Update the specified Report in storage",
     *      tags={"Report"},
     *      description="Update Report",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Report",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Report that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Report")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Report"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportAPIRequest $request)
    {
        /** @var Report $report */
        $report = $this->reportRepository->findWithoutFail($id);

        if (empty($report)) {
            return $this->sendErrorWithData(['Report not found']);
        }

        if ($report->user_id !== \Auth::id()) {
            return $this->sendErrorWithData(['You are not able to update this report']);
        }

        $report = $this->reportRepository->updateRecord($request, $id);

        return $this->sendResponse($report->toArray(), 'Report updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/reports/{id}",
     *      summary="Remove the specified Report from storage",
     *      tags={"Report"},
     *      description="Delete Report",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Report",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var Report $report */
        $report = $this->reportRepository->findWithoutFail($id);

        if (empty($report)) {
            return $this->sendErrorWithData(['Report not found']);
        }

        $this->reportRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Report deleted successfully');
    }
}
