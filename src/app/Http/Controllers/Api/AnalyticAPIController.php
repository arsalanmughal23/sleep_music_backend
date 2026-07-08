<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateAnalyticAPIRequest;
use App\Http\Requests\Api\UpdateAnalyticAPIRequest;
use App\Models\Analytic;
use App\Repositories\Admin\AnalyticRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class AnalyticController
 * @package App\Http\Controllers\Api
 */
class AnalyticAPIController extends AppBaseController
{
    /** @var  AnalyticRepository */
    private $analyticRepository;

    public function __construct(AnalyticRepository $analyticRepo)
    {
        $this->analyticRepository = $analyticRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/analytics",
     *      summary="Get a listing of the Analytics.",
     *      tags={"Analytic"},
     *      description="Get all Analytics",
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
     *                  @SWG\Items(ref="#/definitions/Analytic")
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
        $this->analyticRepository->pushCriteria(new RequestCriteria($request));
        $this->analyticRepository->pushCriteria(new LimitOffsetCriteria($request));
        $analytics = $this->analyticRepository->all();

        return $this->sendResponse($analytics->toArray(), 'Analytics retrieved successfully');
    }

    /**
     * @param CreateAnalyticAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/analytics",
     *      summary="Store a newly created Analytic in storage",
     *      tags={"Analytic"},
     *      description="Store Analytic",
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
     *          description="Analytic that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Analytic")
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
     *                  ref="#/definitions/Analytic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAnalyticAPIRequest $request)
    {
        $analytics = $this->analyticRepository->saveRecord($request);

        return $this->sendResponse($analytics->toArray(), 'Analytic saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/analytics/{id}",
     *      summary="Display the specified Analytic",
     *      tags={"Analytic"},
     *      description="Get Analytic",
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
     *          description="id of Analytic",
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
     *                  ref="#/definitions/Analytic"
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
        /** @var Analytic $analytic */
        $analytic = $this->analyticRepository->findWithoutFail($id);

        if (empty($analytic)) {
            return $this->sendErrorWithData(['Analytic not found']);
        }

        return $this->sendResponse($analytic->toArray(), 'Analytic retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAnalyticAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/analytics/{id}",
     *      summary="Update the specified Analytic in storage",
     *      tags={"Analytic"},
     *      description="Update Analytic",
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
     *          description="id of Analytic",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Analytic that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Analytic")
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
     *                  ref="#/definitions/Analytic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAnalyticAPIRequest $request)
    {
        /** @var Analytic $analytic */
        $analytic = $this->analyticRepository->findWithoutFail($id);

        if (empty($analytic)) {
            return $this->sendErrorWithData(['Analytic not found']);
        }

        $analytic = $this->analyticRepository->updateRecord($request, $id);

        return $this->sendResponse($analytic->toArray(), 'Analytic updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/analytics/{id}",
     *      summary="Remove the specified Analytic from storage",
     *      tags={"Analytic"},
     *      description="Delete Analytic",
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
     *          description="id of Analytic",
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
        /** @var Analytic $analytic */
        $analytic = $this->analyticRepository->findWithoutFail($id);

        if (empty($analytic)) {
            return $this->sendErrorWithData(['Analytic not found']);
        }

        $this->analyticRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Analytic deleted successfully');
    }


}
