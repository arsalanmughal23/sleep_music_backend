<?php

namespace App\Http\Controllers\Api;

use App\Criteria\DeleteTypeCriteria;
use App\Http\Requests\Api\CreateDeleteTypeAPIRequest;
use App\Http\Requests\Api\UpdateDeleteTypeAPIRequest;
use App\Models\DeleteType;
use App\Repositories\Admin\DeleteTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class DeleteTypeController
 * @package App\Http\Controllers\Api
 */
class DeleteTypeAPIController extends AppBaseController
{
    /** @var  DeleteTypeRepository */
    private $deleteTypeRepository;

    public function __construct(DeleteTypeRepository $deleteTypeRepo)
    {
        $this->deleteTypeRepository = $deleteTypeRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/delete-types",
     *      summary="Get a listing of the DeleteTypes.",
     *      tags={"DeleteType"},
     *      description="Get all DeleteTypes",
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
     *                  @SWG\Items(ref="#/definitions/DeleteType")
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
//        $this->deleteTypeRepository->pushCriteria(new RequestCriteria($request));
//        $this->deleteTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
//        $deleteTypes = $this->deleteTypeRepository->all();

        $deleteTypes = $this->deleteTypeRepository
            ->resetCriteria()
            ->pushCriteria(new DeleteTypeCriteria($request->only([
                'status'
            ])))
            ->all();

        return $this->sendResponse($deleteTypes->toArray(), 'Delete Types retrieved successfully');
    }

    /**
     * @param CreateDeleteTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/delete-types",
     *      summary="Store a newly created DeleteType in storage",
     *      tags={"DeleteType"},
     *      description="Store DeleteType",
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
     *          description="DeleteType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeleteType")
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
     *                  ref="#/definitions/DeleteType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDeleteTypeAPIRequest $request)
    {
        $deleteTypes = $this->deleteTypeRepository->saveRecord($request);

        return $this->sendResponse($deleteTypes->toArray(), 'Delete Type saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/delete-types/{id}",
     *      summary="Display the specified DeleteType",
     *      tags={"DeleteType"},
     *      description="Get DeleteType",
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
     *          description="id of DeleteType",
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
     *                  ref="#/definitions/DeleteType"
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
        /** @var DeleteType $deleteType */
        $deleteType = $this->deleteTypeRepository->findWithoutFail($id);

        if (empty($deleteType)) {
            return $this->sendError('Delete Type not found');
        }

        return $this->sendResponse($deleteType->toArray(), 'Delete Type retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDeleteTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/delete-types/{id}",
     *      summary="Update the specified DeleteType in storage",
     *      tags={"DeleteType"},
     *      description="Update DeleteType",
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
     *          description="id of DeleteType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeleteType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeleteType")
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
     *                  ref="#/definitions/DeleteType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDeleteTypeAPIRequest $request)
    {
        /** @var DeleteType $deleteType */
        $deleteType = $this->deleteTypeRepository->findWithoutFail($id);

        if (empty($deleteType)) {
            return $this->sendError('Delete Type not found');
        }

        $deleteType = $this->deleteTypeRepository->updateRecord($request, $id);

        return $this->sendResponse($deleteType->toArray(), 'DeleteType updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/delete-types/{id}",
     *      summary="Remove the specified DeleteType from storage",
     *      tags={"DeleteType"},
     *      description="Delete DeleteType",
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
     *          description="id of DeleteType",
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
        /** @var DeleteType $deleteType */
        $deleteType = $this->deleteTypeRepository->findWithoutFail($id);

        if (empty($deleteType)) {
            return $this->sendError('Delete Type not found');
        }

        $this->deleteTypeRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Delete Type deleted successfully');
    }
}
