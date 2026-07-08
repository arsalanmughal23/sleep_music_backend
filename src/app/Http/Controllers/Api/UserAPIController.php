<?php

namespace App\Http\Controllers\Api;

use App\Criteria\CategoryCriteria;
use App\Criteria\MediaCriteria;
use App\Criteria\PlaylistCriteria;
use App\Criteria\UserCriteria;
use App\Http\Controllers\StripePaymentController;
use App\Http\Requests\Api\CreateUserApiRequest;
use App\Http\Requests\Api\UpdateUserApiRequest;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\MediaRepository;
use App\Repositories\Admin\PlaylistRepository;
use App\Repositories\Admin\UserDetailRepository;
use App\Repositories\Admin\UserRepository;
use App\Traits\RequestCacheable;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\DeleteAccountApiRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */
class UserAPIController extends AppBaseController
{
    use RequestCacheable;

    /** @var  UserRepository */
    private $userRepository;

    /** @var  UserDetailRepository */
    private $detailRepository;

    /** @var  PlaylistRepository */
    private $playlistRepository;

    /** @var  MediaRepository */
    private $mediaRepository;

    /** @var CategoryRepository */
    private $categoryRepository;

    public $reqcSuffix = "user";

    public function __construct(
        UserRepository $userRepo,
        UserDetailRepository $detailRepository,
        PlaylistRepository $playlistRepository,
        MediaRepository $mediaRepository,
        CategoryRepository $categoryRepository
    )
    {
        $this->userRepository     = $userRepo;
        $this->detailRepository   = $detailRepository;
        $this->playlistRepository = $playlistRepository;
        $this->mediaRepository    = $mediaRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/users",
     *      summary="Get a listing of the Users.",
     *      tags={"User"},
     *      description="Get all Users",
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
     *          name="query",
     *          description="Search User by name",
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
     *          name="limit",
     *          description="Change the Default Record Count. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
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
     *                  @SWG\Items(ref="#/definitions/User")
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
        if (isset ($request->user_id) && ($request->is_search)) {
            $id   = $request->user_id;
            $save = app('App\Repositories\Admin\TrendingArtistRepository')->saveRecord($id);
        }
        $criteria = [
            'exclude_empty',
            'sort_by_songs',
            'query',
            'user_id'
        ];
//        $role = $request->get('role', 4);
        // This is hard coded because we should not expose user data until without authentication.
        $criteria = $request->only($criteria);
        $criteria['role'] = Role::ROLE_AUTHENTICATED;
//        $criteria['cacheFor'] = 3600;

        return $this->cacheRequest($request->only($this->mergeDefaultParamsWithControllerParams($criteria)), function () use ($request, $criteria) {
            $users = $this->userRepository
                ->pushCriteria(new RequestCriteria($request))
                ->pushCriteria(new LimitOffsetCriteria($request))
                ->pushCriteria(new UserCriteria($criteria))
                // ->paginate(env("RECORDS_LIMIT", 20));
                ->all();

            return $this->sendResponse($users->toArray(), 'Users retrieved successfully');
        });

    }

    /**
     * @param CreateUserAPIRequest $request
     * @return Response
     *
     * \\@SWG\Post(
     *      path="/users",
     *      summary="Store a newly created Customer in storage",
     *      tags={"User"},
     *      description="Store User",
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
     *          description="User that should be stored",
     *          required=false,
     *          \\@SWG\Schema(ref="#/definitions/User")
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
     *                  ref="#/definitions/User"
     *              ),
     *              \\@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateUserApiRequest $request)
    {

        $userData = $request->only(['name', 'email', 'password']);
        $user     = $this->userRepository->saveRecord($userData);

        return $this->sendResponse($user->toArray(), 'User saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * \\@SWG\Get(
     *      path="/users/{id}",
     *      summary="Display the specified User",
     *      tags={"User"},
     *      description="Get User",
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
     *          description="id of User",
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
     *                  ref="#/definitions/User"
     *              ),
     *              \\@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendErrorWithData(['User not found']);
        }

        return $this->sendResponse($user->toArray(), 'User retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateUserAPIRequest $request
     * @return Response
     *
     * \\@SWG\Put(
     *      path="/users/{id}",
     *      summary="Update the specified User in storage",
     *      tags={"User"},
     *      description="Update User",
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
     *          description="id of User",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      \\@SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User that should be updated",
     *          required=false,
     *          \\@SWG\Schema(ref="#/definitions/User")
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
     *                  ref="#/definitions/User"
     *              ),
     *              \\@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update_user(UpdateUserApiRequest $request)
    {
        $id = \Auth::id();
        /** @var User $user */
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user->id)) {
            return $this->sendErrorWithData(['User not found']);
        }

        if ($user->id != \Auth::id()) {
            return $this->sendErrorWithData(['You are not authorized to update this resource']);
        }
        
        if (!$request->name || !$request->first_name || !$request->last_name) {
            $first_name = $request->first_name ?? $user->first_name;
            $last_name = $request->last_name ?? $user->last_name;
            $fullName = $first_name . ' ' . $last_name;
            
            if(!$request->name){
                $request['name'] = $fullName;
            }
        }
        
        if (strlen($request->name) > 300) {
            throw new \Exception('Full Name must not exceed 300 characters.');
        }

        unset($request['roles']);
        $this->userRepository->updateRecord($request, $user);
        $this->detailRepository->updateRecord($id, $request);
        $user = $this->userRepository->findWithoutFail($id);
        return $this->sendResponse($user->toArray(), 'User updated successfully');
    }

    public function push_notification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_active' => 'required|in:0,1'
        ]);

        if($validator->fails()){
            return $this->sendErrorWithData([$validator->errors()->first()]);
        }

        $user = \Auth::user();
        if(!$user || !$user->details){
            return $this->sendErrorWithData(['User not found']);
        }

        $user->details()->update(['push_notifications' => $request->is_active ?? 0]);
        return $user->refresh();
    }


    /**
     * @param int $id
     * @param UpdateUserAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/profile",
     *      summary="Update the specified User in storage",
     *      tags={"User"},
     *      description="Update User",
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
     *          description="User that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Details")
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
     *                  ref="#/definitions/User"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function profile(Request $request)
    {
        $user = \Auth::id();

        if ($this->detailRepository->updateRecord($user, $request)) {
            return $this->sendResponse($this->userRepository->find($user)->details->toArray(), 'User updated successfully');
        }
        return $this->sendErrorWithData('Something Went Wrong!');

    }

    /**
     * @param int $id
     * @return Response
     *
     * \\@SWG\Delete(
     *      path="/users/{id}",
     *      summary="Remove the specified User from storage",
     *      tags={"User"},
     *      description="Delete User",
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
     *          description="id of User",
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
    
    public function deleteAccount(DeleteAccountApiRequest $request)
    {
        $userID = Auth::id();

        $user = $this->userRepository->findWithoutFail($userID);
        if (empty($user)) {
            return $this->sendErrorWithData(['User not found']);
        }
        
        $isSocialLogin = $user->details->is_social_login ?? null;

        if(!$isSocialLogin && !$request->current_password){
            return $this->sendErrorWithData(['current_password' => 'Current password is required'], 403);
        }

        if((Hash::check($request->current_password, $user->password) && $isSocialLogin == 0) || $isSocialLogin == 1){
            // DB::table('user_details')->where('user_id', $userID)->update([
            //     'delete_type' => $request->delete_type_name,
            //     'delete_reason' => $request->delete_reason,
            // ]);
            $this->userRepository->deleteRecord($userID);
            return $this->sendResponse(null, 'Your account is deleted successfully');
        }

        return $this->sendErrorWithData(['current_password'=>'Wrong password'], 403);
    }
    
    public function destroy($id)
    {
        $AuthID = \Auth::id();
        $id = intval($id);

        if ($id !== $AuthID) {
            return $this->sendErrorWithData(['Invalid ID']);
        }

        $user = $this->userRepository->findWithoutFail($AuthID);
        if (empty($user)) {
            return $this->sendErrorWithData(['User not found']);
        }

        $this->userRepository->deleteRecord($AuthID);

        return $this->sendResponse($AuthID, 'User deleted successfully');
    }

    /**
     * @return Response
     *
     * @SWG\Get(
     *      path="/search",
     *      summary="Search Text on Artist, Playlist, Media",
     *      tags={"Search"},
     *      description="Search Text on Artist=Name, Playlist=Name, Media=Name",
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
     *          name="query",
     *          description="Text to search",
     *          type="string",
     *          required=true,
     *          in="query"
     *      ),     *
     *      @SWG\Parameter(
     *          name="limit",
     *          description="Change the Default Record Count. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
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
    public function search(Request $request)
    {
        $query = $request->get('query');
        $role  = Role::ROLE_AUTHENTICATED;

        $users      = $this->userRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new UserCriteria([
                'role'  => $role,
                'query' => $query
            ]))
            ->all();
        $media      = $this->mediaRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new MediaCriteria([
                'query' => $query
            ]))
            ->all();
        $playlists  = $this->playlistRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new PlaylistCriteria([
                'is_protected' => true,
                'query'        => $query
            ]))
            ->all();
        $categories = $this->categoryRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new CategoryCriteria([
                'query' => $query
            ]))
            ->all();

        return $this->sendResponse([
            'users'      => $users->toArray(),
            'media'      => $media->toArray(),
            'playlists'  => $playlists->toArray(),
            'categories' => $categories->toArray(),
        ], 'Data retrieved successfully');
    }

    public function createAccountLink()
    {
//        dd('hello');
        try {
            $data = app(StripePaymentController::class)->createAccountLink();
            return $this->sendResponse($data['account_links']->toArray(), 'Link generated successfully');
        } catch (\Exception $e) {
            return $this->sendErrorWithData([$e->getMessage()], 403);
        }
    }
}
