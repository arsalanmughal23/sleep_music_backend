<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateMediaviewAPIRequest;
use App\Http\Requests\Api\UpdateMediaviewAPIRequest;
use App\Models\Mediaview;
use App\Repositories\Admin\MediaviewRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class MediaviewController
 * @package App\Http\Controllers\Api
 */

class MediaviewAPIController extends AppBaseController
{
    /** @var  MediaviewRepository */
    private $mediaviewRepository;

    public function __construct(MediaviewRepository $mediaviewRepo)
    {
        $this->mediaviewRepository = $mediaviewRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/mediaviews",
     *      summary="Get a listing of the Mediaviews.",
     *      tags={"Mediaview"},
     *      description="Get all Mediaviews",
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
     *                  @SWG\Items(ref="#/definitions/Mediaview")
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
        $this->mediaviewRepository->pushCriteria(new RequestCriteria($request));
        $this->mediaviewRepository->pushCriteria(new LimitOffsetCriteria($request));
        $mediaviews = $this->mediaviewRepository->all();

        return $this->sendResponse($mediaviews->toArray(), 'Mediaviews retrieved successfully');
    }

    /**
     * @param CreateMediaviewAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/mediaviews",
     *      summary="Store a newly created Mediaview in storage",
     *      tags={"Mediaview"},
     *      description="Store Mediaview",
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
     *          description="Mediaview that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Mediaview")
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
     *                  ref="#/definitions/Mediaview"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMediaviewAPIRequest $request)
    {
        $mediaviews = $this->mediaviewRepository->saveRecord($request);

        return $this->sendResponse($mediaviews->toArray(), 'Mediaview saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/mediaviews/{id}",
     *      summary="Display the specified Mediaview",
     *      tags={"Mediaview"},
     *      description="Get Mediaview",
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
     *          description="id of Mediaview",
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
     *                  ref="#/definitions/Mediaview"
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
        /** @var Mediaview $mediaview */
        $mediaview = $this->mediaviewRepository->findWithoutFail($id);

        if (empty($mediaview)) {
            return $this->sendError('Mediaview not found');
        }

        return $this->sendResponse($mediaview->toArray(), 'Mediaview retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMediaviewAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/mediaviews/{id}",
     *      summary="Update the specified Mediaview in storage",
     *      tags={"Mediaview"},
     *      description="Update Mediaview",
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
     *          description="id of Mediaview",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Mediaview that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Mediaview")
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
     *                  ref="#/definitions/Mediaview"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMediaviewAPIRequest $request)
    {
        /** @var Mediaview $mediaview */
        $mediaview = $this->mediaviewRepository->findWithoutFail($id);

        if (empty($mediaview)) {
            return $this->sendError('Mediaview not found');
        }

        $mediaview = $this->mediaviewRepository->updateRecord($request, $id);

        return $this->sendResponse($mediaview->toArray(), 'Mediaview updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/mediaviews/{id}",
     *      summary="Remove the specified Mediaview from storage",
     *      tags={"Mediaview"},
     *      description="Delete Mediaview",
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
     *          description="id of Mediaview",
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
        /** @var Mediaview $mediaview */
        $mediaview = $this->mediaviewRepository->findWithoutFail($id);

        if (empty($mediaview)) {
            return $this->sendError('Mediaview not found');
        }

        $this->mediaviewRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Mediaview deleted successfully');
    }
}
