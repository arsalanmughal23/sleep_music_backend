<?php

namespace App\Http\Controllers\Api;

use App\Helper\NotificationsHelper;
use App\Http\Requests\Api\CreateAppRatingAPIRequest;
use App\Http\Requests\Api\UpdateAppRatingAPIRequest;
use App\Models\AppRating;
use App\Models\Category;
use App\Models\Media;
use App\Repositories\Admin\AppRatingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class AppRatingController
 * @package App\Http\Controllers\Api
 */
class AppRatingAPIController extends AppBaseController
{
    /** @var  AppRatingRepository */
    private $appRatingRepository;

    public function __construct(AppRatingRepository $appRatingRepo)
    {
        $this->appRatingRepository = $appRatingRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/app-ratings",
     *      summary="Get a listing of the AppRatings.",
     *      tags={"AppRating"},
     *      description="Get all AppRatings",
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
     *                  @SWG\Items(ref="#/definitions/AppRating")
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
        $this->appRatingRepository->pushCriteria(new RequestCriteria($request));
        $this->appRatingRepository->pushCriteria(new LimitOffsetCriteria($request));
        $appRatings = $this->appRatingRepository->all();

        return $this->sendResponse($appRatings->toArray(), 'App Ratings retrieved successfully');
    }

    /**
     * @param CreateAppRatingAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/app-ratings",
     *      summary="Store a newly created AppRating in storage",
     *      tags={"AppRating"},
     *      description="Store AppRating",
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
     *          description="AppRating that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AppRating")
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
     *                  ref="#/definitions/AppRating"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAppRatingAPIRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
//            if (is_null($user->appRating)) {
//                $user->appRating()->create([
//                    'rating' => $request->rating
//                ]);
//                $user->load('appRating');

                $this->assignRandomPrivateMedia($user);

                $message = "Rate our app and unlock exclusive sounds for your sleep experience.";
                $data    = [
                    'notify_type' => 10,   // redirect rate screen app
                ];

                $helperInstance = new NotificationsHelper();
                $helperInstance->sendPushNotifications($message, $user->devices, $data);
//            }
//            $appRatings = $user->appRating;
//            DB::commit();
            return $this->sendResponse($user, 'App Rating saved successfully');
        } catch (\Exception $e) {
//            DB::rollback();
            return $this->sendResponse($e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/app-ratings/{id}",
     *      summary="Display the specified AppRating",
     *      tags={"AppRating"},
     *      description="Get AppRating",
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
     *          description="id of AppRating",
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
     *                  ref="#/definitions/AppRating"
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
        /** @var AppRating $appRating */
        $appRating = $this->appRatingRepository->findWithoutFail($id);

        if (empty($appRating)) {
            return $this->sendError('App Rating not found');
        }

        return $this->sendResponse($appRating->toArray(), 'App Rating retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAppRatingAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/app-ratings/{id}",
     *      summary="Update the specified AppRating in storage",
     *      tags={"AppRating"},
     *      description="Update AppRating",
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
     *          description="id of AppRating",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AppRating that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AppRating")
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
     *                  ref="#/definitions/AppRating"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAppRatingAPIRequest $request)
    {
        /** @var AppRating $appRating */
        $appRating = $this->appRatingRepository->findWithoutFail($id);

        if (empty($appRating)) {
            return $this->sendError('App Rating not found');
        }

        $appRating = $this->appRatingRepository->updateRecord($request, $id);

        return $this->sendResponse($appRating->toArray(), 'AppRating updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/app-ratings/{id}",
     *      summary="Remove the specified AppRating from storage",
     *      tags={"AppRating"},
     *      description="Delete AppRating",
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
     *          description="id of AppRating",
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
        /** @var AppRating $appRating */
        $appRating = $this->appRatingRepository->findWithoutFail($id);

        if (empty($appRating)) {
            return $this->sendError('App Rating not found');
        }

        $this->appRatingRepository->deleteRecord($id);

        return $this->sendResponse($id, 'App Rating deleted successfully');
    }


    public function assignRandomPrivateMedia($user)
    {
        $category                = Category::where('is_unlockable', 1)->first();
        $privateMediaNotAssigned = Media::where('category_id', $category->id)
            ->whereDoesntHave('userMedia', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->inRandomOrder()
            ->first();

        if ($privateMediaNotAssigned) {
            // Create an entry in the user_media table
            $user->userMedia()->create([
                'media_id' => $privateMediaNotAssigned->id,
            ]);
        }

        return $privateMediaNotAssigned;
    }

}
