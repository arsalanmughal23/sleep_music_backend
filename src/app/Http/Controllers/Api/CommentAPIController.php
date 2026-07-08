<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateCommentAPIRequest;
use App\Http\Requests\Api\UpdateCommentAPIRequest;
use App\Models\Comment;
use App\Repositories\Admin\CommentRepository;
use App\Repositories\Admin\MediaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;
use App\Models\Media;
use App\Criteria\CommentCriteria;
use App\Models\Notification;
use App\Models\NotificationUser;


/**
 * Class CommentController
 * @package App\Http\Controllers\Api
 */
class CommentAPIController extends AppBaseController
{
    /** @var  CommentRepository */
    private
        $commentRepository;

    public
    function __construct(CommentRepository $commentRepo)
    {
        $this->commentRepository = $commentRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/comments",
     *      summary="Get a listing of the Comments.",
     *      tags={"Comment"},
     *      description="Get all Comments",
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
     *                  @SWG\Items(ref="#/definitions/Comment")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public
    function index(Request $request)
    {
        $limit  = $request->get('limit', \config('constants.limit'));
        $params = ['media_id', 'parent_id'];
        $this->commentRepository->pushCriteria(new RequestCriteria($request));
        $this->commentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $comments = $this->commentRepository
            ->pushCriteria(new CommentCriteria($request->only($params)))
            ->paginate($limit);
//        $result['total_pages'] = $comments->lastPage();
//        $data                  = $comments->items();
//        $result['data']        = $data;
        return $this->sendResponse($comments, 'Comment retrieved successfully');
    }

    /**
     * @param CreateCommentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/comments",
     *      summary="Store a newly created Comment in storage",
     *      tags={"Comment"},
     *      description="Store Comment",
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
     *          description="Comment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public
    function store(CreateCommentAPIRequest $request)
    {
        $media = app(MediaRepository::class)->findWithoutFail($request->media_id);
        if (!isset($media)) {
            return $this->sendErrorWithData(["Media not does exists"]);
        }
        $comments = $this->commentRepository->saveRecord($request);
        $sent_to  = Media::where([
            'id' => $request->media_id
        ])->value('user_id');
        if ($media->user_id != \Auth::id()) {
            if (isset($request->parent_id)) {
                $notification = Notification::create([
                    'sender_id'   => \Auth::id(),
                    'action_type' => Notification::COMMENT,
                    'ref_id'      => $request->media_id,
                    'message'     => "[name] has replied to your comment.",
                    'status'      => 1
                ]);


                NotificationUser::create([
                    'notification_id' => $notification->id,
                    'user_id'         => $sent_to,
                    'status'          => 1
                ]);
            } else {
                $notification = Notification::create([
                    'sender_id'   => \Auth::id(),
                    'action_type' => Notification::COMMENT,
                    'ref_id'      => $request->media_id,
                    'message'     => "[name] has commented on your media.",
                    'status'      => 1
                ]);


                NotificationUser::create([
                    'notification_id' => $notification->id,
                    'user_id'         => $sent_to,
                    'status'          => 1
                ]);
            }


        } else {
            if ($request->user_id) {

                if ($request->user_id != \Auth::id()) {

                    $notification = Notification::create([
                        'sender_id'   => \Auth::id(),
                        'action_type' => Notification::COMMENT,
                        'ref_id'      => $request->media_id,
                        'message'     => "[name] has replied to your comment.",
                        'status'      => 1
                    ]);


                    NotificationUser::create([
                        'notification_id' => $notification->id,
                        'user_id'         => $request->user_id,
                        'status'          => 1
                    ]);

                }
            }

        }

        return $this->sendResponse($comments->toArray(), 'Comment saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/comments/{id}",
     *      summary="Display the specified Comment",
     *      tags={"Comment"},
     *      description="Get Comment",
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
     *          description="id of Comment",
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
     *                  ref="#/definitions/Comment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public
    function show($id)
    {
        /** @var Comment $comment */
        $comment = $this->commentRepository->findWithoutFail($id);

        if (empty($comment)) {
            return $this->sendError('Comment not found');
        }

        return $this->sendResponse($comment->toArray(), 'Comment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCommentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/comments/{id}",
     *      summary="Update the specified Comment in storage",
     *      tags={"Comment"},
     *      description="Update Comment",
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
     *          description="id of Comment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Comment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Comment")
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
     *                  ref="#/definitions/Comment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public
    function update_comment(UpdateCommentAPIRequest $request)
    {
        $id = $request->comment_id;
        /** @var Comment $comment */
        $comment = $this->commentRepository->findWithoutFail($id);
        if (empty($comment)) {
            return $this->sendErrorWithData(['Comment not found']);
        }
        // dd($comment->user_id, \Auth::id());
        if ($comment->user_id != \Auth::id()) {
            return $this->sendErrorWithData(['You are not authorized to modify this comment']);
        }

        $comment = $this->commentRepository->updateRecord($request, $id);

        return $this->sendResponse($comment->toArray(), 'Comment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/comments/{id}",
     *      summary="Remove the specified Comment from storage",
     *      tags={"Comment"},
     *      description="Delete Comment",
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
     *          description="id of Comment",
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
    public
    function destroy($id)
    {
        /** @var Comment $comment */
        $comment = $this->commentRepository->findWithoutFail($id);

        if (empty($comment)) {
            return $this->sendErrorWithData(['Comment not found']);
        }

        $this->commentRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Comment deleted successfully');
    }

    public
    function getCommentsByMedia(Request $request)
    {

        return Comment::where("user_id", "=", $request->id)->simplePaginate(5);
    }
}
