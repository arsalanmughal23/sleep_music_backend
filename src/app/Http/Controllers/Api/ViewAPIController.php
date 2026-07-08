<?php

namespace App\Http\Controllers\Api;

use App\Criteria\ViewsCriteria;
use App\Http\Requests\Api\CreateViewAPIRequest;
use App\Http\Requests\Api\UpdateViewAPIRequest;
use App\Models\View;
use App\Repositories\Admin\ViewRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class ViewController
 * @package App\Http\Controllers\Api
 */
class ViewAPIController extends AppBaseController
{
    /** @var  ViewRepository */
    private $viewRepository;

    public function __construct(ViewRepository $viewRepo)
    {
        $this->viewRepository = $viewRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/views",
     *      summary="Get a listing of the Views.",
     *      tags={"View"},
     *      description="Get all Views",
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
     *                  @SWG\Items(ref="#/definitions/View")
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
        $limit = $request->get('limit', \config('constants.limit'));
        $this->viewRepository->pushCriteria(new RequestCriteria($request));
        $this->viewRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->viewRepository->pushCriteria(new ViewsCriteria([
            'user_id' => \Auth::id()
        ]));

        $views = $this->viewRepository->paginate($limit);

        return $this->sendResponse($views->toArray(), 'Views retrieved successfully');
    }

    /**
     * @param CreateViewAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/views",
     *      summary="Store a newly created View in storage",
     *      tags={"View"},
     *      description="Store View",
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
     *          description="View that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/View")
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
     *                  ref="#/definitions/View"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateViewAPIRequest $request)
    {
        try {
            $views = $this->viewRepository->saveRecord($request);

            return $this->sendResponse($views->toArray(), 'User Exercise saved successfully');

        } catch (\Exception $exception) {
            if ($exception->getCode() == 23000) {
                return $this->sendErrorWithData(["Some server error"]);
            }
            return $this->sendErrorWithData([$exception->getMessage()]);
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/views/{id}",
     *      summary="Display the specified View",
     *      tags={"View"},
     *      description="Get View",
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
     *          description="id of View",
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
     *                  ref="#/definitions/View"
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
        /** @var View $view */
        $view = $this->viewRepository->findWithoutFail($id);

        if (empty($view)) {
            return $this->sendError('View not found');
        }

        return $this->sendResponse($view->toArray(), 'View retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateViewAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/views/{id}",
     *      summary="Update the specified View in storage",
     *      tags={"View"},
     *      description="Update View",
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
     *          description="id of View",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="View that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/View")
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
     *                  ref="#/definitions/View"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateViewAPIRequest $request)
    {
        /** @var View $view */
        $view = $this->viewRepository->findWithoutFail($id);

        if (empty($view)) {
            return $this->sendError('View not found');
        }

        $view = $this->viewRepository->updateRecord($request, $id);

        return $this->sendResponse($view->toArray(), 'View updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/views/{id}",
     *      summary="Remove the specified View from storage",
     *      tags={"View"},
     *      description="Delete View",
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
     *          description="id of View",
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
        /** @var View $view */
        $view = $this->viewRepository->findWithoutFail($id);

        if (empty($view)) {
            return $this->sendError('View not found');
        }

        $this->viewRepository->deleteRecord($id);

        return $this->sendResponse($id, 'View deleted successfully');
    }
}
