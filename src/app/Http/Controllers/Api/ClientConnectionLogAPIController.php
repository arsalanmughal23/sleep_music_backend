<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateClientConnectionLogAPIRequest;
use App\Http\Requests\Api\UpdateClientConnectionLogAPIRequest;
use App\Models\ClientConnectionLog;
use App\Repositories\Admin\ClientConnectionLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class ClientConnectionLogController
 * @package App\Http\Controllers\Api
 */
class ClientConnectionLogAPIController extends AppBaseController
{
    /** @var  ClientConnectionLogRepository */
    private $clientConnectionLogRepository;

    public function __construct(ClientConnectionLogRepository $clientConnectionLogRepo)
    {
        $this->clientConnectionLogRepository = $clientConnectionLogRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/client-connection-logs",
     *      summary="Get a listing of the ClientConnectionLogs.",
     *      tags={"ClientConnectionLog"},
     *      description="Get all ClientConnectionLogs",
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
     *                  @SWG\Items(ref="#/definitions/ClientConnectionLog")
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
        $this->clientConnectionLogRepository->pushCriteria(new RequestCriteria($request));
        $this->clientConnectionLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $clientConnectionLogs = $this->clientConnectionLogRepository->all();

        return $this->sendResponse($clientConnectionLogs->toArray(), 'Client Connection Logs retrieved successfully');
    }

    /**
     * @param CreateClientConnectionLogAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/client-connection-logs",
     *      summary="Store a newly created ClientConnectionLog in storage",
     *      tags={"ClientConnectionLog"},
     *      description="Store ClientConnectionLog",
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
     *          description="ClientConnectionLog that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ClientConnectionLog")
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
     *                  ref="#/definitions/ClientConnectionLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateClientConnectionLogAPIRequest $request)
    {
        $clientConnectionLogs = $this->clientConnectionLogRepository->saveRecord($request);

        return $this->sendResponse($clientConnectionLogs->toArray(), 'Client Connection Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/client-connection-logs/{id}",
     *      summary="Display the specified ClientConnectionLog",
     *      tags={"ClientConnectionLog"},
     *      description="Get ClientConnectionLog",
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
     *          description="id of ClientConnectionLog",
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
     *                  ref="#/definitions/ClientConnectionLog"
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
        /** @var ClientConnectionLog $clientConnectionLog */
        $clientConnectionLog = $this->clientConnectionLogRepository->findWithoutFail($id);

        if (empty($clientConnectionLog)) {
            return $this->sendErrorWithData(['Client Connection Log not found']);
        }

        return $this->sendResponse($clientConnectionLog->toArray(), 'Client Connection Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateClientConnectionLogAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/client-connection-logs/{id}",
     *      summary="Update the specified ClientConnectionLog in storage",
     *      tags={"ClientConnectionLog"},
     *      description="Update ClientConnectionLog",
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
     *          description="id of ClientConnectionLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ClientConnectionLog that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ClientConnectionLog")
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
     *                  ref="#/definitions/ClientConnectionLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateClientConnectionLogAPIRequest $request)
    {
        /** @var ClientConnectionLog $clientConnectionLog */
        $clientConnectionLog = $this->clientConnectionLogRepository->findWithoutFail($id);

        if (empty($clientConnectionLog)) {
            return $this->sendErrorWithData(['Client Connection Log not found']);
        }

        $clientConnectionLog = $this->clientConnectionLogRepository->updateRecord($request, $id);

        return $this->sendResponse($clientConnectionLog->toArray(), 'ClientConnectionLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/client-connection-logs/{id}",
     *      summary="Remove the specified ClientConnectionLog from storage",
     *      tags={"ClientConnectionLog"},
     *      description="Delete ClientConnectionLog",
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
     *          description="id of ClientConnectionLog",
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
        /** @var ClientConnectionLog $clientConnectionLog */
        $clientConnectionLog = $this->clientConnectionLogRepository->findWithoutFail($id);

        if (empty($clientConnectionLog)) {
            return $this->sendErrorWithData(['Client Connection Log not found']);
        }

        $this->clientConnectionLogRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Client Connection Log deleted successfully');
    }
}
