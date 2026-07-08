<?php

namespace App\Http\Controllers\Api;

use App\Criteria\TrendingArtistCriteria;
use App\Http\Requests\Api\CreateTrendingArtistAPIRequest;
use App\Http\Requests\Api\UpdateTrendingArtistAPIRequest;
use App\Models\TrendingArtist;
use App\Repositories\Admin\TrendingArtistRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class TrendingArtistController
 * @package App\Http\Controllers\Api
 */
class TrendingArtistAPIController extends AppBaseController
{
    /** @var  TrendingArtistRepository */
    private $trendingArtistRepository;

    public function __construct(TrendingArtistRepository $trendingArtistRepo)
    {
        $this->trendingArtistRepository = $trendingArtistRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/trending-artists",
     *      summary="Get a listing of the TrendingArtists.",
     *      tags={"TrendingArtist"},
     *      description="Get all TrendingArtists",
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
     *                  @SWG\Items(ref="#/definitions/TrendingArtist")
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
        $limit           = $request->get('limit', \config('constants.limit'));
        $trendingArtists = $this->trendingArtistRepository
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new TrendingArtistCriteria())
            ->paginate($limit);


        return $this->sendResponse($trendingArtists, 'Trending Artists retrieved successfully');
    }

    /**
     * @param CreateTrendingArtistAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/trending-artists",
     *      summary="Store a newly created TrendingArtist in storage",
     *      tags={"TrendingArtist"},
     *      description="Store TrendingArtist",
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
     *          description="TrendingArtist that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TrendingArtist")
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
     *                  ref="#/definitions/TrendingArtist"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTrendingArtistAPIRequest $request)
    {
        $trendingArtists = $this->trendingArtistRepository->saveRecord($request);

        return $this->sendResponse($trendingArtists->toArray(), 'Trending Artist saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/trending-artists/{id}",
     *      summary="Display the specified TrendingArtist",
     *      tags={"TrendingArtist"},
     *      description="Get TrendingArtist",
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
     *          description="id of TrendingArtist",
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
     *                  ref="#/definitions/TrendingArtist"
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
        /** @var TrendingArtist $trendingArtist */
        $trendingArtist = $this->trendingArtistRepository->findWithoutFail($id);

        if (empty($trendingArtist)) {
            return $this->sendError('Trending Artist not found');
        }

        return $this->sendResponse($trendingArtist->toArray(), 'Trending Artist retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTrendingArtistAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/trending-artists/{id}",
     *      summary="Update the specified TrendingArtist in storage",
     *      tags={"TrendingArtist"},
     *      description="Update TrendingArtist",
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
     *          description="id of TrendingArtist",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TrendingArtist that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TrendingArtist")
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
     *                  ref="#/definitions/TrendingArtist"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTrendingArtistAPIRequest $request)
    {
        /** @var TrendingArtist $trendingArtist */
        $trendingArtist = $this->trendingArtistRepository->findWithoutFail($id);

        if (empty($trendingArtist)) {
            return $this->sendError('Trending Artist not found');
        }

        $trendingArtist = $this->trendingArtistRepository->updateRecord($request, $id);

        return $this->sendResponse($trendingArtist->toArray(), 'TrendingArtist updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/trending-artists/{id}",
     *      summary="Remove the specified TrendingArtist from storage",
     *      tags={"TrendingArtist"},
     *      description="Delete TrendingArtist",
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
     *          description="id of TrendingArtist",
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
        /** @var TrendingArtist $trendingArtist */
        $trendingArtist = $this->trendingArtistRepository->findWithoutFail($id);

        if (empty($trendingArtist)) {
            return $this->sendError('Trending Artist not found');
        }

        $this->trendingArtistRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Trending Artist deleted successfully');
    }
}
