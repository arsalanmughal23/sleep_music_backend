<?php

namespace App\Http\Controllers\Api;

use App\Criteria\MediaCriteria;
use App\Http\Requests\Api\CreateMediaAPIRequest;
use App\Http\Requests\Api\UpdateMediaAPIRequest;
use App\Models\Follow;
use App\Models\Media;
use App\Repositories\Admin\MediaRepository;
use App\Repositories\Admin\MediaviewRepository;
use App\Traits\RequestCacheable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

use Symfony\Contracts\Service\Attribute\Required;
use App\Repositories\Admin\UserRepository;
use App\Models\User;
use App\Models\Analytic;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

/**
 * Class MediaController
 * @package App\Http\Controllers\Api
 */
class MediaAPIController extends AppBaseController
{
    use RequestCacheable;

    /** @var  MediaRepository */
    private $mediaRepository;

    public $reqcSuffix = "media";

    public function __construct(MediaRepository $mediaRepo)
    {
        $this->mediaRepository = $mediaRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/media",
     *      summary="Get a listing of the Media.",
     *      tags={"Media"},
     *      description="Get all Media",
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
     *          name="is_featured",
     *          description="Acceptable values are 0 and 1, 0 will return exclude featured media, 1 will return only featured media,. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="category_id",
     *          description="Filter media with this category id. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="playlist_id",
     *          description="Filter media with this playlist id. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="user_id",
     *          description="Filter media with this user id. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="type",
     *          description="Filter media with type. If not found, Returns All Records in DB.",
     *          type="integer",
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
     *                  @SWG\Items(ref="#/definitions/Media")
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
        $params = ['category_id', 'is_mixer', 'is_unlockable', 'user_id', 'query'];

        if ($request->get('is_mine', false)) {
            $this->avoidCache = true;
        }

        $limit = $request->get('limit', \config('constants.limit'));
        $media = $this->mediaRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new MediaCriteria($request->only($params)))
            ->paginate($limit);

        return $this->sendResponse($media, 'Media retrieved successfully');

    }

    /**
     * @param CreateMediaAPIRequest $request
     * @return Response
     *
     * \\@SWG\Post(
     *      path="/media",
     *      summary="Store a newly created Media in storage",
     *      tags={"Media"},
     *      description="Store Media",
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
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          \\@SWG\Schema(ref="#/definitions/Media")
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
     *                  ref="#/definitions/Media"
     *              ),
     *              \\@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMediaAPIRequest $request)
    {

        $media = $this->mediaRepository->saveRecord($request);
        if (isset($request->media_length)) {
            if ($request->media_length <= 30)
                DB::table('user_media')->insert(
                    ['user_id'  => \Auth::id(),
                     'media_id' => $media->id]
                );
        }

        //  DB::statement("INSERT INTO notifications_admin (message) VALUES ('New Media Created $media->id)'");

        return $this->sendResponse($media->toArray(), 'Media saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/media/{id}",
     *      summary="Display the specified Media",
     *      tags={"Media"},
     *      description="Get Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
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
     *                  ref="#/definitions/Media"
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
        /** @var Media $media */
        $media = $this->mediaRepository->findWithoutFail($id);

        if (empty($media)) {
            return $this->sendErrorWithData(['Media not found']);
        }

        $views = Media::where('id', $media->id)->value('views');
        $views = $views + 1;
        Media::where('id', $id)->update(['views' => $views]);

        app(MediaviewRepository::class)->saveRecord($media->id);


        $views_cat = Category::where('id', $media->category_id)->value('views');
        $views_cat = $views_cat + 1;
        Category::where('id', $id)->update(['views' => $views_cat]);

        return $this->sendResponse($media->toArray(), 'Media retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMediaAPIRequest $request
     * @return Response
     *
     * \\@SWG\Put(
     *      path="/media/{id}",
     *      summary="Update the specified Media in storage",
     *      tags={"Media"},
     *      description="Update Media",
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
     *          description="id of Media",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      \\@SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be updated",
     *          required=false,
     *          \\@SWG\Schema(ref="#/definitions/Media")
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
     *                  ref="#/definitions/Media"
     *              ),
     *              \\@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMediaAPIRequest $request)
    {

        /** @var Media $media */
        $media = $this->mediaRepository->findWithoutFail($id);

        if (empty($media)) {
            return $this->sendErrorWithData(['Media not found']);
        }

        $media = $this->mediaRepository->updateRecord($request, $id);

        return $this->sendResponse($media->toArray(), 'Media updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * \\@SWG\Delete(
     *      path="/media/{id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Media"},
     *      description="Delete Media",
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
     *          description="id of Media",
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
        /** @var Media $media */
        $media = $this->mediaRepository->findWithoutFail($id);

        if (empty($media)) {
            return $this->sendErrorWithData(['Media not found']);
        }

        $this->mediaRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Media deleted successfully');
    }

    public function mediaViews(Request $request)
    {
        $id    = $request->media_id;
        $media = $this->mediaRepository->findWithoutFail($request->media_id);

        if (empty($media)) {
            return $this->sendErrorWithData(['Media not found']);
        }

        $views = Media::where('id', $media->id)->value('views');
        $views = $views + 1;
        Media::where('id', $id)->update(['views' => $views]);


        $views_cat = Category::where('id', $media->category_id)->value('views');
        $views_cat = $views_cat + 1;
        Category::where('id', $id)->update(['views' => $views_cat]);
        return $this->sendResponse(True, 'Media successfully Incremented');
    }

    public function yourMusic(Request $request)
    {
        $mscat = DB::table('views')->select('category_id', DB::raw('count(category_id) as m'))->orderBy('m', 'DESC')->groupBy('category_id')->take(5)->get();
        if ($mscat[0]->category_id) {
            for ($i = 0; $i < count($mscat); $i++) {
                $category[$i] = $mscat[$i]->category_id;
            }
        }
        $msart = DB::table('trending_artist')->select('artist_id', DB::raw('count(artist_id) as m'))->groupBy('artist_id')->orderBy('m', 'DESC')->take(5)->get();

        if ($msart[0]->artist_id) {
            for ($i = 0; $i < count($msart); $i++) {
                $artist[$i] = $msart[$i]->artist_id;
            }
        }
//        dd($artist);
        $limit = $request->get('limit', \config('constants.limit'));
        $media = $this->mediaRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new MediaCriteria([
                'artist'   => $artist,
                'category' => $category,
            ]))
            ->paginate($limit);

//        $media = $media->shuffle();
//        $media = $media->paginate($limit);


//        $media = Media::where('user_id', $artist)->orwhere('category_id', $category)->orderBy('id', 'desc')->paginate(\config('constants.limit'));

        return $this->sendResponse($media, 'Media retrieved successfully');
    }


    public function GetFollowingMedia(Request $request)
    {

        $folowed = Follow::where('followed_by_user_id', \Auth::id())->get();
        if ((count($folowed) > 0)) {
            if ($folowed[0]->followed_by_user_id) {
                for ($i = 0; $i < count($folowed); $i++) {
                    $follow[$i] = $folowed[$i]->followed_user_id;
                }

            }
            $follows = 0;
        } else {
//            return $this->sendResponse('[]', 'Media not found');
            $follows = 1;

        }
        if ($follows != 1) {

            $limit = $request->get('limit', \config('constants.limit'));
            $media = $this->mediaRepository
                ->resetCriteria()
                ->pushCriteria(new RequestCriteria($request))
                ->pushCriteria(new LimitOffsetCriteria($request))
                ->pushCriteria(new MediaCriteria([
                    'usersx_id' => $follow,
                ]))
                ->paginate($limit);
        } else {

            $limit = $request->get('limit', \config('constants.limit'));
            $media = $this->mediaRepository
                ->resetCriteria()
                ->pushCriteria(new RequestCriteria($request))
                ->pushCriteria(new LimitOffsetCriteria($request))
                ->pushCriteria(new MediaCriteria([
                    'check' => $follows,
                ]))
                ->paginate($limit);
        }


        return $this->sendResponse($media, 'Media retrieved successfully');
    }


//    public function paginate($items, $perPage = 10, $page = null, $options = [])
//    {
//
////        dd(array_values($items));
//        $page  = $page ?: (Paginator::resolveCurrentPage() ?: 1);
//        $items = $items instanceof Collection ? $items : Collection::make($items);
//        $item  = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
//
//        return $item->toArray();
//    }


    public function paginate($items, $perPage = null, $page = null)
    {
        $page        = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total       = count($items);
        $currentpage = $page;
        $offset      = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);
        return new LengthAwarePaginator($itemstoshow, $total, $perPage);
    }


    public function yourMusics(Request $request)
    {


        $mscat = DB::table('views')->select('category_id', DB::raw('count(category_id) as m'))->orderBy('m', 'DESC')->groupBy('category_id')->take(5)->get();
        if ($mscat[0]->category_id) {
            for ($i = 0;
                 $i < count($mscat);
                 $i++) {
                $category[$i] = $mscat[$i]->category_id;
            }
        }
        $msart = DB::table('trending_artist')->select('artist_id', DB::raw('count(artist_id) as m'))->groupBy('artist_id')->orderBy('m', 'DESC')->take(5)->get();

        if ($msart[0]->artist_id) {
            for ($i = 0; $i < count($msart); $i++) {
                $artist[$i] = $msart[$i]->artist_id;
            }
        }
        $tt = null;

        $check = DB::table('user_media')->select('media_id')->where('user_id', \Auth::id())->where('flag', 0)->where('deleted_at', NULL)->orderBy('flag', 'ASC')->get();
        if ($check->isEmpty()) {
            $media_ids[] = null;
        } else {
            for ($i = 0; $i < count($check); $i++) {
                $media_ids[] = $check[$i]->media_id;
            }
        }

        $limit = $request->get('limit', \config('constants.limit'));
        $media = $this->mediaRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            // ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new MediaCriteria([
                'artist'   => $artist,
                'category' => $category,

            ]))->get();

        $media2 = $this->mediaRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            //->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new MediaCriteria([
                //  'checksum'  => 1,
                'media_ids' => $media_ids,
            ]))->get();

        $media = array_merge($media2->toArray(), $media->toArray());
        if (isset($request->limit)) {
            $limit = (int)$request->limit;
            $media = $this->paginate($media, $limit);
        } else {
            $media = $this->paginate($media, 10);
        }

//        }


        DB::table('user_media')->where('user_id', \Auth::id())->update(['flag' => 1]);
//        return $this->sendResponse(['temp_media' => $media2, 'trending' => $media], 'Media retrieved successfully');
        return $this->sendResponse($media, 'Media retrieved successfully');
    }


}
