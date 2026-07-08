<?php

namespace App\Http\Controllers\Api;


use App\Http\Requests\Api\UpdateLikeAPIRequest;
use App\Models\Like;
use App\Repositories\Admin\LikeRepository;
use App\Repositories\Admin\MediaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use PharIo\Manifest\AuthorCollectionIterator;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;
use App\Criteria\LikeCriteria;
use App\Models\Notification;
use App\Models\Media;
use App\Models\NotificationUser;


/**
 * Class LikeController
 * @package App\Http\Controllers\Api
 */
class LikeAPIController extends AppBaseController
{
    /** @var  LikeRepository */
    private $likeRepository;

    public function __construct(LikeRepository $likeRepo)
    {
        $this->likeRepository = $likeRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/likes",
     *      summary="Get a listing of the Likes.",
     *      tags={"Like"},
     *      description="Get all Likes",
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
     *                  @SWG\Items(ref="#/definitions/Like")
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
        $likes = $this->likeRepository
            ->pushCriteria(new RequestCriteria($request))
//            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new LikeCriteria($request->only([
                'is_mine',
                'media_id',
                'is_popular'
            ])))
            ->paginate($limit);
//        dd('sdsd');

//        $result['total_pages'] = $likes->lastPage();
//        $data                  = $likes->items();
//        $result['data']        = $data;
        return $this->sendResponse($likes, 'Likes retrieved successfully');
    }

    /**
     * @param CreateLikeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/likes",
     *      summary="Store a newly created Like in storage",
     *      tags={"Like"},
     *      description="Store Like",
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
     *          description="Like that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Like")
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
     *                  ref="#/definitions/Like"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        /*$like_exists = $this->likeRepository->findWhere([
            'user_id'  => \Auth::id(),
            'media_id' => $request->media_id
        ])->count();
        if ($like_exists) {
            return $this->sendErrorWithData(["Like already exists"]);
        }*/
        $unlike = $request->input('unlike', null);
        if (isset($unlike) && $unlike == 1) {
            $this->likeRepository->where([
                'user_id'  => \Auth::id(),
                'media_id' => $request->media_id
            ])->delete();
            $media = app(MediaRepository::class)->findWithoutFail($request->media_id);
            return $this->sendResponse($media->toArray(), 'Unlike successfully');
        }
//        $request['is_liked'] = 1;
        $likes   = $this->likeRepository->saveRecord($request);
        $sent_to = Media::where([
            'id' => $request->media_id
        ])->value('user_id');
        if ($sent_to != \Auth::id()) {


            $notification = Notification::create([
                'sender_id'   => \Auth::id(),
                'action_type' => Notification::LIKE,
                'ref_id'      => $request->media_id,
                'message'     => "[name] has liked your media.",
                'status'      => 1
            ]);

            NotificationUser::create([
                'notification_id' => $notification->id,
                'user_id'         => (int)$sent_to,
                'status'          => 1
            ]);
        }
        $media = app(MediaRepository::class)->findWithoutFail($request->media_id);
        return $this->sendResponse($media->toArray(), 'Liked successfully');
    }


    public function unLike(Request $request)
    {

        $like_exists = Like::where('user_id', '=', \Auth::id())
            ->where('media_id', '=', (int)$request->media_id)
            ->count();

        if (!$like_exists) {
            return $this->sendErrorWithData(['Like does not exist']);
        } else {
            $user = Like::where([['user_id', '=', \Auth::id()], ['media_id', '=', $request->media_id]])->first();
            $user->delete();
        }


        return $this->sendResponse([], 'Unliked successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/likes/{id}",
     *      summary="Display the specified Like",
     *      tags={"Like"},
     *      description="Get Like",
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
     *          description="id of Like",
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
     *                  ref="#/definitions/Like"
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
        /** @var Like $like */
        $like = $this->likeRepository->findWithoutFail($id);

        if (empty($like)) {
            return $this->sendErrorWithData(['Like not found']);
        }

        return $this->sendResponse($like->toArray(), 'Like retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateLikeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/likes/{id}",
     *      summary="Update the specified Like in storage",
     *      tags={"Like"},
     *      description="Update Like",
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
     *          description="id of Like",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Like that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Like")
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
     *                  ref="#/definitions/Like"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, Request $request)
    {
        /** @var Like $like */
        $like = $this->likeRepository->findWithoutFail($id);

        if (empty($like)) {
            return $this->sendErrorWithData(['Like not found']);
        }

        $like = $this->likeRepository->updateRecord($request, $id);

        return $this->sendResponse($like->toArray(), 'Like updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/likes/{id}",
     *      summary="Remove the specified Like from storage",
     *      tags={"Like"},
     *      description="Delete Like",
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
     *          description="id of Like",
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
        /** @var Like $like */
        $like = $this->likeRepository->findWithoutFail($id);

        if (empty($like)) {
            return $this->sendErrorWithData(['Like not found']);
        }

        $this->likeRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Like deleted successfully');
    }
}
