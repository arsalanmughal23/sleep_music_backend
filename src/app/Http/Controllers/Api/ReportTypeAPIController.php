<?php

namespace App\Http\Controllers\Api;

use App\Criteria\ReportTypeCriteria;
use App\Http\Requests\Api\CreateReportTypeAPIRequest;
use App\Http\Requests\Api\UpdateReportTypeAPIRequest;
use App\Models\ReportType;
use App\Repositories\Admin\ReportTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class ReportTypeController
 * @package App\Http\Controllers\Api
 */
class ReportTypeAPIController extends AppBaseController
{
    /** @var  ReportTypeRepository */
    private $reportTypeRepository;

    public function __construct(ReportTypeRepository $reportTypeRepo)
    {
        $this->reportTypeRepository = $reportTypeRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/report-types",
     *      summary="Get a listing of the ReportTypes.",
     *      tags={"ReportType"},
     *      description="Get all ReportTypes",
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
     *                  @SWG\Items(ref="#/definitions/ReportType")
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
//        dd('hello world');
        // $this->reportTypeRepository->pushCriteria(new RequestCriteria($request));
        //$this->reportTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $limit = $request->get('limit', \config('constants.limit'));

        $reportTypes = $this->reportTypeRepository
            ->resetCriteria()
            ->pushCriteria(new ReportTypeCriteria($request->only([
                'type'
            ])))
            ->paginate($limit);

        return $this->sendResponse($reportTypes->toArray(), 'Report Types retrieved successfully');
    }

    /**
     * @param CreateReportTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/report-types",
     *      summary="Store a newly created ReportType in storage",
     *      tags={"ReportType"},
     *      description="Store ReportType",
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
     *          description="ReportType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportType")
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
     *                  ref="#/definitions/ReportType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReportTypeAPIRequest $request)
    {
        $reportTypes = $this->reportTypeRepository->saveRecord($request);

        return $this->sendResponse($reportTypes->toArray(), 'Report Type saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/report-types/{id}",
     *      summary="Display the specified ReportType",
     *      tags={"ReportType"},
     *      description="Get ReportType",
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
     *          description="id of ReportType",
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
     *                  ref="#/definitions/ReportType"
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
        /** @var ReportType $reportType */
        $reportType = $this->reportTypeRepository->findWithoutFail($id);

        if (empty($reportType)) {
            return $this->sendErrorWithData(['Report Type not found']);
        }

        return $this->sendResponse($reportType->toArray(), 'Report Type retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateReportTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/report-types/{id}",
     *      summary="Update the specified ReportType in storage",
     *      tags={"ReportType"},
     *      description="Update ReportType",
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
     *          description="id of ReportType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ReportType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ReportType")
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
     *                  ref="#/definitions/ReportType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReportTypeAPIRequest $request)
    {
        /** @var ReportType $reportType */
        $reportType = $this->reportTypeRepository->findWithoutFail($id);

        if (empty($reportType)) {
            return $this->sendErrorWithData(['Report Type not found']);
        }

        $reportType = $this->reportTypeRepository->updateRecord($request, $id);

        return $this->sendResponse($reportType->toArray(), 'ReportType updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/report-types/{id}",
     *      summary="Remove the specified ReportType from storage",
     *      tags={"ReportType"},
     *      description="Delete ReportType",
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
     *          description="id of ReportType",
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
        /** @var ReportType $reportType */
        $reportType = $this->reportTypeRepository->findWithoutFail($id);

        if (empty($reportType)) {
            return $this->sendErrorWithData(['Report Type not found']);
        }

        $this->reportTypeRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Report Type deleted successfully');
    }
}
