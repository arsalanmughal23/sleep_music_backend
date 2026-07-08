<?php

namespace App\Http\Controllers\Api;

use App\Constants\EmailServiceTemplateNames;
use App\Helper\SendEmailHelper;
use App\Helper\Util;
use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Requests\Api\ChangePasswordAPIRequest;
use App\Http\Requests\Api\DrmCallbackRequest;
use App\Http\Requests\Api\ForgotPasswordCodeRequest;
use App\Http\Requests\Api\LoginAPIRequest;
use App\Http\Requests\Api\RegistrationAPIRequest;
use App\Http\Requests\Api\SocialLoginAPIRequest;
use App\Http\Requests\Api\UpdateForgotPasswordRequest;
use App\Http\Requests\Api\VerifyCodeRequest;
use App\Jobs\SendEmail;
use App\Models\Role;
use App\Repositories\Admin\MediaRepository;
use App\Repositories\Admin\SocialAccountRepository;
use App\Repositories\Admin\UDeviceRepository;
use App\Repositories\Admin\UserDetailRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\UserDetail;
use App\Models\User;
use Error;

/**
 * Class AuthAPIController
 * @package App\Http\Controllers\Api
 */
class AuthAPIController extends AppBaseController
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserDetailRepository
     */
    protected $userDetailRepository;

    /**
     * @var UDeviceRepository
     */
    protected $uDevice;

    /**
     * @var SocialAccountRepository
     */
    protected $socialAccountRepository;

    /** @var MediaRepository */
    protected $mediaRepository;

    /**
     * AuthAPIController constructor.
     * @param UserRepository $userRepo
     * @param UserDetailRepository $userDetailRepo
     * @param UDeviceRepository $uDeviceRepo
     * @param SocialAccountRepository $socialAccountRepo
     * @param MediaRepository $mediaRepository
     */
    public function __construct(UserRepository $userRepo, UserDetailRepository $userDetailRepo, UDeviceRepository $uDeviceRepo, SocialAccountRepository $socialAccountRepo, MediaRepository $mediaRepository)
    {
        $this->userRepository          = $userRepo;
        $this->userDetailRepository    = $userDetailRepo;
        $this->uDevice                 = $uDeviceRepo;
        $this->socialAccountRepository = $socialAccountRepo;
        $this->mediaRepository         = $mediaRepository;
    }

    /**
     * @param RegistrationAPIRequest $request
     * @return \Illuminate\Http\JsonResponse|mixed
     *
     * @SWG\Post(
     *      path="/register",
     *      summary="Register a new user.",
     *      tags={"Authorization"},
     *      description="Register User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Register")
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
     *                  ref="#/definitions/Register"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    
    public function register(RegistrationAPIRequest $request)
    {
        try {
            // if (!$request->name) {
            //     $request['name'] = $request->first_name . ' ' . $request->last_name;
            // }
            // if (strlen($request->name) > 300) {
            //     throw new \Exception('Full Name must not exceed 300 characters.');
            // }
            $user = $this->userRepository->getUserByEmail($request->email);
            if (!empty($user)) {
                throw new \Exception('User already exists');
            }
            $user = $this->userRepository->saveRecord($request);
            $this->userDetailRepository->saveRecord($user->id, $request);

            // check if device token exists
            if (property_exists($request, 'device_token')) {
                $this->uDevice->saveRecord($user->id, $request);
            }

            //attach role to user....
            $this->userRepository->attachRole($user->id, [Role::ROLE_AUTHENTICATED]);
            // $customer = app(StripePaymentController::class)->createCustomer($user->email);
            // $this->userDetailRepository->model()::where('user_id', $user->id)->update([
            //     'stripe_customer_id' => $customer->id
            // ]);
            $credentials = [
                'email'    => $request->email,
                'password' => $request->password
            ];

            $subject = "User Email Verification Code";
            $this->send_otp_now($request->email, 'email', $subject);
            //   DB::statement("INSERT INTO notifications_admin (message) VALUES ('New User Created $user->id')");

            if (!$token = auth()->guard('api')->attempt($credentials)) {
                return $this->sendErrorWithData(["Invalid Login Credentials"], 403);
            }
            return $this->respondWithToken($token, $request);

        } catch (\Exception $e) {
            return $this->sendErrorWithData([$e->getMessage()], 500);
        }
    }

    /**
     * @param LoginAPIRequest $request
     * @return \Illuminate\Http\JsonResponse|mixed
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *      path="/login",
     *      summary="Login a user.",
     *      tags={"Authorization"},
     *      description="Login User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="User that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Login")
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
     *                  ref="#/definitions/Login"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function login(LoginAPIRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return $this->sendErrorWithData([
                "loginFailed" => "Invalid Login Credentials"
            ], 403, null);
        }
        $user = $this->userRepository->getUserByEmail($request->email);
        /*
        if (isset($user->details) && !$user->details->is_verified) {
            return $this->sendErrorWithData(["Email not verified"]);
        }
        */

        return $this->respondWithToken($token, $request);
    }

    /**
     * @param SocialLoginAPIRequest $request
     * @return mixed
     *
     * @SWG\Post(
     *      path="/social_login",
     *      summary="Login With Social Account.",
     *      tags={"Authorization"},
     *      description="Login With Social Account.",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Login With Social Account.",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SocialAccounts")
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
     *                  ref="#/definitions/SocialAccounts"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function socialLogin(SocialLoginAPIRequest $request)
    {
        try{
            $user    = false;
            $input   = $request->all();
            $account = $this->socialAccountRepository->findWhere(['platform' => $input['platform'], 'client_id' => $input['client_id'], 'deleted_at' => null])->first();
            $userEmail = $input['email'] ?? null;
            $userName = $input['name'] ?? null;
            $firstName = $input['first_name'] ?? null;
            $lastName = $input['last_name'] ?? null;

            if ($account && $account->user ?? null) {
                // Account found. generate token;
                $user = $account->user;
            } else {
                if($request->platform == 'apple'){
                    $jwtDecodeResponse = Util::JWTDecodeUserInfo($request->token);
                    $jwtUserInfo = $jwtDecodeResponse['data'];
                    $userEmail  = $jwtUserInfo['email'];

                    $emailName  = explode('@', $userEmail)[0];

                    // Replace & Explode: Number & Special Character with [SPACE] Seperator from {$emailName}
                    $emailNameParts = explode(' ', preg_replace('/[0-9\W]+/', ' ', $emailName));
                    $firstName  = $emailNameParts[0];
                    $lastName   = $emailNameParts[1] ?? null;
                    $userName   = ($firstName && $lastName) ? $firstName.' '.$lastName : $firstName;
                }

                // Check if email address already exists. if yes, then link social account. else register new user.
                if (isset($userEmail)) {
                    $user = $this->userRepository->findWhere(['email' => $userEmail])->first();
                }

                // Check User is exists with Non-Social-User and Verified
                if(($user->details->is_social_login ?? 0) == 0 && ($user->details->is_verified ?? 0) == 1){
                    throw new Error('The email has already been taken.');
                }

                if (!$user) {
                    // Register user with only social details and no password.
                    $userData             = [];
                    $userData['name']     = $userName ?? "user_" . $input['client_id'];
                    $userData['email']    = $userEmail ?? $input['client_id'] . '_' . $input['platform'] . '@' . config('app.name') . '.com';
                    $userData['password'] = bcrypt(substr(str_shuffle(MD5(microtime())), 0, 12));
                    $user                 = $this->userRepository->create($userData);
                    //attach role to user....
                    $this->userRepository->attachRole($user->id, [Role::ROLE_AUTHENTICATED]);
                    $userDetails['user_id']    = $user->id;
                    $userDetails['first_name'] = $firstName;
                    $userDetails['last_name'] = $lastName;
                    $userDetails['email_updates']   = 1;
                    $userDetails['is_social_login'] = 1;
                    $userDetails['is_verified']     = 1;

                    $this->userDetailRepository->create($userDetails);
                }
                // Add social media link to the user
                $this->socialAccountRepository->saveRecord($user->id, $request);
            }

            if (isset($userName)) {
                $user->name = $userName;
                $user->save();
            }

            //update social login user image
            $userDetails = [];
            if(isset($input['image'])) $userDetails['image'] = $input['image'];
            if(isset($input['first_name'])) $userDetails['first_name'] = $input['first_name'];
            if(isset($input['last_name'])) $userDetails['last_name'] = $input['last_name'];

            if(count($userDetails)){
                $this->userDetailRepository->model()::where('user_id', $user->id)->update($userDetails);
            }

            if (!$token = JWTAuth::fromUser($user)) {
                throw new Error('Invalid credentials, please try login again');
            }
            return $this->respondWithToken($token, $request);

        }catch(Error $e){
            return $this->sendErrorWithData([$e->getMessage()]);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *      path="/me",
     *      summary="user profile.",
     *      tags={"Authorization"},
     *      description="user profile.",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
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
     *
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function me()
    {
        return $this->sendResponse(auth()->user(), 'My Profile');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     * @SWG\Post(
     *      path="/refresh",
     *      summary="refresh auth token.",
     *      tags={"Authorization"},
     *      description="refresh auth token.",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
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
     *
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function refresh(Request $request)
    {
        // FIXME: Find a better fix. This is not a good workaround. but working fine.
        auth()->guard('api')->factory()->setTTL(config('jwt.refresh_ttl'));
        return $this->respondWithToken(auth()->guard('api')->refresh(false, true), $request);
//        return $this->respondWithToken(auth()->guard('api')->refresh(), $request);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *      path="/logout",
     *      summary="logout user.",
     *      tags={"Authorization"},
     *      description="logout user.",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
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
     *
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $token = Auth::guard('api')->getToken();
        if (!$token) {
            return $this->sendErrorWithData(["Token" => "Token not provided"], 403);
        }

        // Add token to the blacklist
        $user->devices()->delete();

        Auth::guard('api')->invalidate($token);
        auth()->guard('api')->logout($token);
        return $this->sendResponse([], 'Successfully logged out');
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param array $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, Request $request)
    {

        $user = auth()->guard('api')->setToken($token)->user()->toArray();
        // check if device token exists
        if ($request->has('device_token')) {
            $this->uDevice->saveRecord($user['id'], $request);
        }
        $user = array_merge($user, [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->guard('api')->factory()->getTTL() * 60
        ]);
        return $this->sendResponse($user, 'Logged in successfully');
    }

    /**
     * @param ForgotPasswordCodeRequest $request
     * @return mixed
     *
     * @SWG\Get(
     *      path="/forget-password",
     *      summary="Forget password request.",
     *      tags={"Passwords"},
     *      description="Register User",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="email",
     *          description="User email",
     *          type="string",
     *          required=true,
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
     *
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function forgetPassword(ForgotPasswordCodeRequest $request)
    {

        $user = $this->userRepository->getUserByEmail($request->email);
        if (!$user) {
            return $this->sendErrorWithData(["Email" => "Your email address was not found."], 403);
        }

        $subject = "Forgot Password Verification Code";
        try {
            $this->send_otp_now($request->email, 'password', $subject);

        } catch (\Exception $e) {

            return $this->sendErrorWithData([$e->getMessage()], 403);
        }
        return $this->sendResponse([], 'Verification Code Send To Your Email');
    }


    public function verifyUserPasswordOTPCode(VerifyCodeRequest $request)
    {
        $code = $request->verification_code;

        $user = $this->userRepository->getUserByEmail($request->email);
        if (!$user) {
            return $this->sendErrorWithData(["Email" => "Your email address was not found."], 403);
        }

        $check = DB::table('password_resets')->where(['email' => $user->email, 'code' => $code])->first();
        if (!is_null($check)) {
            $data['email'] = $check->email;
            $data['verification_code']  = "valid";
            // DB::table('password_resets')->where(['email' => $user->email, 'code' => $code])->delete();

            return $this->sendResponse($data, 'Verified');
        } else {
            return $this->sendErrorWithData(['Code Is Invalid'], 403);
        }
    }


    /**
     * @param VerifyCodeRequest $request
     * @return mixed
     *
     * @SWG\Post(
     *      path="/verify-reset-code",
     *      summary="verify forget password request code.",
     *      tags={"Passwords"},
     *      description="verify code",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="verification_code",
     *          description="verification code",
     *          type="integer",
     *          required=true,
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
     *
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function verifyCodeUser(VerifyCodeRequest $request)
    {
        $code = $request->verification_code;

        $check = DB::table('password_resets')->where('code', $code)->first();
        if (!is_null($check)) {
            $data['email'] = $check->email;
            $data['code']  = "valid";
            return $this->sendResponse(['user' => $data], 'Verified');
        } else {
            return $this->sendErrorWithData(['Code Is Invalid'], 403);
        }
    }

    /**
     * @param UpdateForgotPasswordRequest $request
     * @return mixed
     *
     * @SWG\Post(
     *      path="/reset-password",
     *      summary="Reset password.",
     *      tags={"Passwords"},
     *      description="Reset password.",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="email",
     *          description="user email ",
     *          type="string",
     *          required=true,
     *          in="query"
     *      ),
     *     @SWG\Parameter(
     *          name="verification_code",
     *          description="verification code",
     *          type="integer",
     *          required=true,
     *          in="query"
     *      ),@SWG\Parameter(
     *          name="password",
     *          description="new password",
     *          type="string",
     *          required=true,
     *          in="query"
     *      ),@SWG\Parameter(
     *          name="password_confirmation",
     *          description="confirm password",
     *          type="string",
     *          required=true,
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
     *
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function resetPassword(UpdateForgotPasswordRequest $request)
    {
        $code = $request->verification_code;

        $check = DB::table('password_resets')->where(['code' => $code, 'email' => $request->email])->first();
        if (!is_null($check)) {
            $postData['password'] = bcrypt($request->password);
            try {
                $data = $this->userRepository->getUserByEmail($request->email);
                $user = $this->userRepository->update($postData, $data->id);
                DB::table('password_resets')->where(['code' => $code, 'email' => $request->email])->delete();
                return $this->sendResponse(null, 'Password Changed');
            } catch (\Exception $e) {
                return $this->sendErrorWithData([$e->getMessage()], 403);
            }
        } else {
            return $this->sendErrorWithData(['Code Is Invalid'], 403);
        }
    }

    /**
     * @param ChangePasswordAPIRequest $request
     * @return mixed
     *
     * @SWG\Post(
     *      path="/change-password",
     *      summary="Change password.",
     *      tags={"Passwords"},
     *      description="Change Password password.",
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
     *          name="current_password",
     *          description="Current Password",
     *          type="string",
     *          required=true,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="password",
     *          description="new password",
     *          type="string",
     *          required=true,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="password_confirmation",
     *          description="confirm password",
     *          type="string",
     *          required=true,
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function changePassword(ChangePasswordAPIRequest $request)
    {
        $user = Auth::user();

        if (Hash::check($request->current_password, $user->password)) {
            if ($request->current_password == $request->password){
                return $this->sendErrorWithData(['New password must be different from current password'], 403);
            }
            $this->userRepository->update(['password' => bcrypt($request->password)], $user->id);
            return $this->sendResponse(null, 'Password Successfully Updated');
        }
        return $this->sendErrorWithData(['Current Password is incorrect.'], 403);
    }

    public function drmCallback(DrmCallbackRequest $request)
    {
        $user = false;
        try {
            $user = auth()->guard('api')->setToken($request->get('session'))->authenticate();
        } catch (\Exception $e) {
        }
        if (!($user && $user->id == $request->get('user'))) {
            return \Response::json([
                "message" => "Session Not Found",
//                "redirectUrl" => "http://url.to.error.page.if.supported.by.drm"
            ]);
        }
        $media_id = explode("-", $request->asset);
        if (count($media_id) > 1) {
            list($media_id, $variation) = $media_id;
        } else {
            $media_id = $media_id[0];
        }
        $media = $this->mediaRepository->findWithoutFail($media_id);
        if (!$media) {
            return \Response::json([
                "message" => "Media Record Not Found",
//                "redirectUrl" => "http://url.to.error.page.if.supported.by.drm"
            ]);
        }
        $crt = [
            // Log the server giving the license
            "accountingId" => config("constants.server_id"),
            // DRMToday Asset ID, saved in the DB
            "assetId"      => $media_id,

            "profile" => [
                "rental" => [
                    // After acquiring the license, user can start his playback for next 12 hours
                    "relativeExpiration" => "PT12H",
                    // This is how long an asset can be played once it is started the first time.
                    "playDuration"       => $media->media_length * 2 * 1000
                ]
            ],

            "outputProtection" => [
                // It seems that these enables/disables the appropriate protections.
                "digital"  => true,
                "analogue" => true,
                // enable hard output protection (i.e. disables “best effort” approach).
                "enforce"  => false,
            ]
        ];

        /*if (!empty($variation)) {
            // Variant id can be used for different resolutions.
            // By Default, we use the HD variant of an asset.
            $crt["variantId"] = $variation;
        }*/
        return \Response::json($crt);
    }


    public function usernameExists(Request $request)
    {

        if (Userdetail::where('username', '=', $request->username)->count()) {
            return $this->sendErrorWithData(["Username exists"]);
        } else  return $this->sendResponse([], 'Username does not exist');

    }

    public function resendOTPCode(ForgotPasswordCodeRequest $request)
    {
        $user = $this->userRepository->getUserByEmail($request->email);
        if (!$user) {
            return $this->sendErrorWithData(["Email" => "Your email address was not found."], 403);
        }

        $type = $request->type ?? 'email';

        $subject = "Your Verification Code";
        try {
            $this->send_otp_now($request->email, $type, $subject);

        } catch (\Exception $e) {

            return $this->sendErrorWithData([$e->getMessage()], 403);
        }
        return $this->sendResponse(null, 'Verification Code Send To Your Email');
    }


    public function verifyUserEmailVerificationOTPCode(VerifyCodeRequest $request)
    {
        $code = $request->input('verification_code');
        $email = $request->input('email');
        $user  = User::where('email', $email)->first();

        if (!$user) {
            return $this->sendErrorWithData(['Email is in-valid'], 403);
        }

        $check = DB::table('verify_email')->where(['user_id' => $user->id, 'code' => $code])->first();
        if (!is_null($check)) {
            DB::table('verify_email')->where(['user_id' => $user->id, 'code' => $code])->delete();

            DB::table('user_details')
                ->where('user_id', $user->id)
                ->update(['is_verified' => true]);

            return $this->sendResponse(null, 'Verified');
        } else {
            return $this->sendErrorWithData(['Code Is Invalid'], 403);
        }
    }

    function send_otp_now($email, $type, $subject)
    {
        $tableName = null;
        switch ($type) {
            case 'email' :
                $tableName = 'verify_email';
                break;
            case 'password' :
                $tableName = 'password_resets';
                break;
            default :
                break;
        }

        $user = $this->userRepository->getUserByEmail($email);
        if (!$user || !$tableName) {
            return 0;
        }
        $code = rand(1111, 9999);

        try {
            $email = $user->email;
            $data = [
                'first_name' => $user->details->first_name ?? 'User',
                'otp_code' => $code
            ];

            $check = DB::table($tableName)->where('user_id', $user->id)->first();
            if ($check) {
                DB::table($tableName)->where('user_id', $user->id)->delete();
            }
            DB::table($tableName)->insert(['user_id' => $user->id, 'email' => $user->email, 'code' => $code, 'created_at' => Carbon::now()]);
            
            $sendEmailJob = new SendEmail($email, $subject, $data, EmailServiceTemplateNames::OTP_TEMPLATE);
            dispatch($sendEmailJob);

        } catch (\Exception $e) {
            return $this->sendErrorWithData([$e->getMessage()], 403);
        }
    }
}