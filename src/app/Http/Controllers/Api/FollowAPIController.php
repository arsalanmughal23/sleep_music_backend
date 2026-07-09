<?php

namespace App\Http\Controllers\Api;

use App\Criteria\FollowCriteria;
use App\Http\Requests\Api\CreateFollowAPIRequest;
use App\Http\Requests\Api\UpdateFollowAPIRequest;
use App\Models\Follow;
use App\Models\Notification;
use App\Repositories\Admin\FollowRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;
use DB;
use App\Models\NotificationUser;

/**
 * Class FollowController
 * @package App\Http\Controllers\Api
 */
class FollowAPIController extends AppBaseController
{
    /** @var  FollowRepository */
    private $followRepository;

    public function __construct(FollowRepository $followRepo)
    {
        $this->followRepository = $followRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/follows",
     *      summary="Get a listing of the Follows.",
     *      tags={"Follow"},
     *      description="Get all Follows",
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
     *                  @SWG\Items(ref="#/definitions/Follow")
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
        $limit   = $request->get('limit', \config('constants.limit'));
        $follows = $this->followRepository
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new FollowCriteria($request->only([
                'is_popular_artist',
                'is_mine',
                'user_id'
            ])))
            ->paginate($limit);

//        $result['total_pages'] = $follows->lastPage();
//        $data                  = $follows->items();
//        $result['data']        = $data;
        return $this->sendResponse($follows, 'Follows retrieved successfully');
    }

    /**
     * @param CreateFollowAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/follows",
     *      summary="Store a newly created Follow in storage",
     *      tags={"Follow"},
     *      description="Store Follow",
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
     *          description="Follow that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Follow")
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
     *                  ref="#/definitions/Follow"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFollowAPIRequest $request)
    {
        if (isset($request->unfollow) && $request->unfollow == 1) {
            $res = $this->followRepository->where([
                "followed_user_id"    => $request->followed_user_id,
                'followed_by_user_id' => \Auth::id()
            ])->delete();
            return $this->sendResponse($res, 'UnFollow successfully');
        }
        $count = $this->followRepository->findWhere([
            "followed_user_id"    => $request->followed_user_id,
            'followed_by_user_id' => \Auth::id()
        ])->count();
        if ($count < 1) {
            $follows = $this->followRepository->saveRecord($request);

            $sent_to = $request->followed_user_id;
            if ($sent_to != \Auth::id()) {
                $notification = Notification::create([
                    'sender_id'   => \Auth::id(),
                    'action_type' => Notification::FOLLOW,
                    'ref_id'      => \Auth::id(),
                    'message'     => "[name] has followed you.",
                    'status'      => 1
                ]);

                NotificationUser::create([
                    'notification_id' => $notification->id,
                    'user_id'         => (int)$sent_to,
                    'status'          => 1
                ]);
            }
            return $this->sendResponse($follows->toArray(), 'Follow saved successfully');
        }
        return $this->sendErrorWithData(['Already Followed']);
    }


    public function removefollower(Request $request)
    {
        $id = \Auth::id();
        if ($request->follower_id) {
            $res = $this->followRepository->where([
                "followed_user_id"    => $id,
                'followed_by_user_id' => $request->follower_id
            ])->delete();
            return $this->sendResponse($res, 'Follower deleted successfully');
        }
        return $this->sendErrorWithData(['Follower not Found ']);

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/follows/{id}",
     *      summary="Display the specified Follow",
     *      tags={"Follow"},
     *      description="Get Follow",
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
     *          description="id of Follow",
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
     *                  ref="#/definitions/Follow"
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
        /** @var Follow $follow */
        $follow = $this->followRepository->findWithoutFail($id);

        if (empty($follow)) {
            return $this->sendErrorWithData(['Follow not found']);
        }

        return $this->sendResponse($follow->toArray(), 'Follow retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFollowAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/follows/{id}",
     *      summary="Update the specified Follow in storage",
     *      tags={"Follow"},
     *      description="Update Follow",
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
     *          description="id of Follow",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Follow that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Follow")
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
     *                  ref="#/definitions/Follow"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFollowAPIRequest $request)
    {
        /** @var Follow $follow */
        $follow = $this->followRepository->findWithoutFail($id);

        if (empty($follow)) {
            return $this->sendErrorWithData(['Follow not found']);
        }

        $follow = $this->followRepository->updateRecord($request, $id);

        return $this->sendResponse($follow->toArray(), 'Follow updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/follows/{id}",
     *      summary="Remove the specified Follow from storage",
     *      tags={"Follow"},
     *      description="Delete Follow",
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
     *          description="id of Follow",
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
        /** @var Follow $follow */
        $follow = $this->followRepository->findWithoutFail($id);

        if (empty($follow)) {
            return $this->sendErrorWithData(['Follow not found']);
        }

        $this->followRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Follow deleted successfully');
    }

    public function followers(Request $request)
    {
        $count = Follow::where("followed_user_id", "=", $request->id)->get()->count();
        return $this->sendResponse($count, 'Retreived total followers successfully');
    }


    public function unfollow($id)
    {
        $follow = $this->followRepository->findWhere([
            'followed_by_user_id' => \Auth::id(),
            'followed_user_id'    => $id
        ])->first();
        if (!isset($follow)) {
            return $this->sendErrorWithData(["Record not found"]);
        }
        $follow->delete();
        return $this->sendResponse([], 'unfollowed successfully');
    }


    public function following(Request $request)
    {

        $limit   = $request->get('limit', \config('constants.limit'));
        $follows = $this->followRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new FollowCriteria($request->only([
                'is_mine',
                'following',
                'user_id_folow'
            ])))
            ->paginate($limit);


//        $result['total_pages'] = $follows->lastPage();
//        $data                  = $follows->items();
//        $result['data']        = $data;
        return $this->sendResponse($follows, 'Following List retrieved successfully');
    }


    public function followers_list(Request $request)
    {
        $limit   = $request->get('limit', \config('constants.limit'));
        $follows = $this->followRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new FollowCriteria($request->only([
                'is_mine',
                'followers',
                'user_id_folow'
            ])))
            ->paginate($limit);


//        $result['total_pages'] = $follows->lastPage();
//        $data                  = $follows->items();
//        $result['data']        = $data;
        return $this->sendResponse($follows, 'Following List retrieved successfully');
    }
}
