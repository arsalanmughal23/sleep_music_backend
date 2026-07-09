<?php

namespace App\Http\Controllers\Api;

use App\Criteria\NotifcationUserCriteria;
use App\Http\Requests\Api\CreateNotificationUserAPIRequest;
use App\Http\Requests\Api\UpdateNotificationUserAPIRequest;
use App\Models\NotificationUser;
use App\Repositories\Admin\NotificationUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class NotificationUserController
 * @package App\Http\Controllers\Api
 */
class NotificationUserAPIController extends AppBaseController
{
    /** @var  NotificationUserRepository */
    private $notificationUserRepository;

    public function __construct(NotificationUserRepository $notificationUserRepo)
    {
        $this->notificationUserRepository = $notificationUserRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/notification-users",
     *      summary="Get a listing of the NotificationUsers.",
     *      tags={"NotificationUser"},
     *      description="Get all NotificationUsers",
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
     *                  @SWG\Items(ref="#/definitions/NotificationUser")
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
        $this->notificationUserRepository->pushCriteria(new RequestCriteria($request));
        $this->notificationUserRepository->pushCriteria(new LimitOffsetCriteria($request));
        $notificationUsers = $this->notificationUserRepository->pushCriteria(new NotifcationUserCriteria($request->only([
            'is_mine'
        ])))
            ->paginate($limit);

//        $notificationUsers = $this->notificationUserRepository->all();
        foreach ($notificationUsers as $notiuser) {
            if (isset($notiuser->notification->sender->name)) {
                if (strpos($notiuser->notification->message, '[name]') !== false) {
                    if ($notiuser->notification->sender->name) {

                        $notiuser->notification->message = str_replace('[name]', $notiuser->notification->sender->name, $notiuser->notification->message);
                    }
                }
            } else {
                unset($notiuser);
            }


        }

        return $this->sendResponse($notificationUsers->toArray(), 'Notification Users retrieved successfully');
    }

    /**
     * @param CreateNotificationUserAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/notification-users",
     *      summary="Store a newly created NotificationUser in storage",
     *      tags={"NotificationUser"},
     *      description="Store NotificationUser",
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
     *          description="NotificationUser that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationUser")
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
     *                  ref="#/definitions/NotificationUser"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateNotificationUserAPIRequest $request)
    {
        $notificationUsers = $this->notificationUserRepository->saveRecord($request);

        return $this->sendResponse($notificationUsers->toArray(), 'Notification User saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/notification-users/{id}",
     *      summary="Display the specified NotificationUser",
     *      tags={"NotificationUser"},
     *      description="Get NotificationUser",
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
     *          description="id of NotificationUser",
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
     *                  ref="#/definitions/NotificationUser"
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
        /** @var NotificationUser $notificationUser */
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            return $this->sendError('Notification User not found');
        }

        return $this->sendResponse($notificationUser->toArray(), 'Notification User retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateNotificationUserAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/notification-users/{id}",
     *      summary="Update the specified NotificationUser in storage",
     *      tags={"NotificationUser"},
     *      description="Update NotificationUser",
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
     *          description="id of NotificationUser",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="NotificationUser that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/NotificationUser")
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
     *                  ref="#/definitions/NotificationUser"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateNotificationUserAPIRequest $request)
    {
        /** @var NotificationUser $notificationUser */
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            return $this->sendError('Notification User not found');
        }

        $notificationUser = $this->notificationUserRepository->updateRecord($request, $id);

        return $this->sendResponse($notificationUser->toArray(), 'NotificationUser updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/notification-users/{id}",
     *      summary="Remove the specified NotificationUser from storage",
     *      tags={"NotificationUser"},
     *      description="Delete NotificationUser",
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
     *          description="id of NotificationUser",
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
        /** @var NotificationUser $notificationUser */
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            return $this->sendError('Notification User not found');
        }

        $this->notificationUserRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Notification User deleted successfully');
    }
}
