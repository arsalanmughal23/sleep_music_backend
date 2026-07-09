<?php

namespace App\Http\Controllers\Api;

use App\Criteria\CategoryCriteria;
use App\Http\Requests\Api\CreateCategoryAPIRequest;
use App\Http\Requests\Api\UpdateCategoryAPIRequest;
use App\Models\Category;
use App\Repositories\Admin\CategoryRepository;
use App\Traits\RequestCacheable;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class CategoryController
 * @package App\Http\Controllers\Api
 */
class CategoryAPIController extends AppBaseController
{
    use RequestCacheable;

    /** @var  CategoryRepository */
    private $categoryRepository;

    public $reqcSuffix = "category";

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepository = $categoryRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/categories",
     *      summary="Get a listing of the Categories.",
     *      tags={"Category"},
     *      description="Get all Categories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="type",
     *          description="Acceptable values are 10 and 20, 10 will return only Audio (Music) Categories, 20 will return only Video Categories. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="with_media",
     *          description="Add media records in along with data. If not found only category objects are returned",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="media_is_featured",
     *          description="Media Filter: Include media records that are marked as featured. If set to 0, include media records that are not marked as featured. If not found, do not apply any condition on featured param.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="with_playlists",
     *          description="Add playlist records in along with data. If not found only category objects are returned",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="playlist_is_featured",
     *          description="Playlist Filter: Include playlist records that are marked as featured. If set to 0, include playlist records that are not marked as featured. If not found, do not apply any condition on featured param.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="playlist_parent_id",
     *          description="Filter playlist by parent_id. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="playlist_parent_only",
     *          description="Get parent playlists only. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="playlist_child_only",
     *          description="Get child playlists only. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="playlist_has_child",
     *          description="Filter playlist by child status, 1 will only include playlists that has children, 0 will only include playlist that does not have children. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="exclude_empty",
     *          description="Acceptable values are 0 and 1, 1 will exclude all artists that does not have any songs selected in them, 0 will return all artists.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="sort_by_songs",
     *          description="Sort all artists be song count desc.",
     *          type="integer",
     *          default=1,
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="orderBy",
     *          description="Pass the property name you want to sort your response. If not found, Returns All Records in DB without sorting.",
     *          type="string",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="sortedBy",
     *          description="Pass 'asc' or 'desc' to define the sorting method. If not found, 'asc' will be used by default",
     *          type="string",
     *          required=false,
     *          in="query"
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
     *                  @SWG\Items(ref="#/definitions/Category")
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
        $request['exclude_empty'] = 1;
        $params = [
            // 'media_is_featured',
            // 'with_playlists',
            // 'playlist_is_featured',
            // 'playlist_parent_only',
            // 'playlist_child_only',
            // 'playlist_parent_id',
            // 'playlist_has_child',
            // 'sort_by_songs',
            // 'trending',
            // 'popular',
            
            'is_mixer',
            'type',
            'with_media',
            'query',
            'exclude_empty'
        ];
        return $this->cacheRequest($request->only($this->mergeDefaultParamsWithControllerParams($params)), function () use ($request, $params) {
            $limit = $request->get('limit', \config('constants.limit'));

            $categories = $this->categoryRepository
                ->resetCriteria()
                ->pushCriteria(new RequestCriteria($request))
                ->pushCriteria(new LimitOffsetCriteria($request))
                ->pushCriteria(new CategoryCriteria($request->only($params)))
                ->orderBy('created_at', 'asc')->paginate($limit);
//            $result['total_pages'] = $categories->lastPage();
//            $result['data']        = $categories->items();
            return $this->sendResponse($categories, 'Categories retrieved successfully');
        });
    }
    public function categoriesWithSounds(Request $request)
    {
        $request['exclude_empty'] = 1;
        $params = [
            // 'media_is_featured',
            // 'with_playlists',
            // 'playlist_is_featured',
            // 'playlist_parent_only',
            // 'playlist_child_only',
            // 'playlist_parent_id',
            // 'playlist_has_child',
            // 'sort_by_songs',
            // 'trending',
            // 'popular',
            
            'is_mixer',
            'type',
            'with_media',
            'query',
            'exclude_empty'
        ];

        return $this->cacheRequest($request->only($this->mergeDefaultParamsWithControllerParams($params)), function () use ($request, $params) {
            $limit = $request->get('limit', \config('constants.limit'));
            
            $categories = $this->categoryRepository
                ->resetCriteria()
                ->pushCriteria(new RequestCriteria($request))
                ->pushCriteria(new LimitOffsetCriteria($request))
                ->pushCriteria(new CategoryCriteria($request->only($params)))
                ->orderBy('created_at', 'asc')->paginate($limit);

            return $this->sendResponse($categories, 'Categories retrieved successfully');
        });
    }

    /**
     * @param CreateCategoryAPIRequest $request
     * @return Response
     *
     * \\@SWG\Post(
     *      path="/categories",
     *      summary="Store a newly created Category in storage",
     *      tags={"Category"},
     *      description="Store Category",
     *      produces={"application/json"},
     *      \\@SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Category that should be stored",
     *          required=false,
     *          \\@SWG\Schema(ref="#/definitions/Category")
     *      ),
     *      \\@SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          \\@SWG\Schema(
     *              type="object",
     *              \\@SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              \\@SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Category"
     *              ),
     *              \\@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCategoryAPIRequest $request)
    {
        $categories = $this->categoryRepository->saveRecord($request);

        //  DB::statement("INSERT INTO notifications_admin (message) VALUES ('New Category Created $categories->id)'");

        return $this->sendResponse($categories->toArray(), 'Category saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/categories/{id}",
     *      summary="Display the specified Category",
     *      tags={"Category"},
     *      description="Get Category",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Category",
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
     *                  ref="#/definitions/Category"
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
        /** @var Category $category */
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            return $this->sendErrorWithData(['Category not found']);
        }

        return $this->sendResponse($category->toArray(), 'Category retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCategoryAPIRequest $request
     * @return Response
     *
     * \\@SWG\Put(
     *      path="/categories/{id}",
     *      summary="Update the specified Category in storage",
     *      tags={"Category"},
     *      description="Update Category",
     *      produces={"application/json"},
     *      \\@SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      \\@SWG\Parameter(
     *          name="id",
     *          description="id of Category",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      \\@SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Category that should be updated",
     *          required=false,
     *          \\@SWG\Schema(ref="#/definitions/Category")
     *      ),
     *      \\@SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          \\@SWG\Schema(
     *              type="object",
     *              \\@SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              \\@SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Category"
     *              ),
     *              \\@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCategoryAPIRequest $request)
    {
        /** @var Category $category */
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            return $this->sendError(['Category not found']);
        }

        $category = $this->categoryRepository->updateRecord($request, $id);

        return $this->sendResponse($category->toArray(), 'Category updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * \\@SWG\Delete(
     *      path="/categories/{id}",
     *      summary="Remove the specified Category from storage",
     *      tags={"Category"},
     *      description="Delete Category",
     *      produces={"application/json"},
     *      \\@SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      \\@SWG\Parameter(
     *          name="id",
     *          description="id of Category",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      \\@SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          \\@SWG\Schema(
     *              type="object",
     *              \\@SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              \\@SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              \\@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var Category $category */
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            return $this->sendError(['Category not found']);
        }

        $this->categoryRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Category deleted successfully');
    }


}
