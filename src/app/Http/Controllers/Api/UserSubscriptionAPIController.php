<?php

namespace App\Http\Controllers\Api;

use App\Helper\Util;
use App\Http\Requests\Api\CreateUserSubscriptionAPIRequest;
use App\Http\Requests\Api\UpdateUserSubscriptionAPIRequest;
use App\Jobs\AndroidUserSubscriptionWebHook;
use App\Models\Transaction;
use App\Models\UserSubscription;
use App\Models\WebhookLog;
use App\Repositories\Admin\TransactionRepository;
use App\Repositories\Admin\UserSubscriptionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Admin\UserDetailRepository;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
use App\Helper\NotificationsHelper;
use App\Repositories\Admin\UserRepository;

/**
 * Class UserSubscriptionController
 * @package App\Http\Controllers\Api
 */

class UserSubscriptionAPIController extends AppBaseController
{
    /** @var  UserSubscriptionRepository */
    private $userSubscriptionRepository;

    public function __construct(UserSubscriptionRepository $userSubscriptionRepo)
    {
        $this->userSubscriptionRepository = $userSubscriptionRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/user-subscriptions",
     *      summary="Get a listing of the UserSubscriptions.",
     *      tags={"UserSubscription"},
     *      description="Get all UserSubscriptions",
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
     *                  @SWG\Items(ref="#/definitions/UserSubscription")
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
        $query = $request->only(['status']);
        // $this->userSubscriptionRepository->pushCriteria(new RequestCriteria($request));
        // $this->userSubscriptionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $userSubscriptions = $this->userSubscriptionRepository->where($query)->get();

        return $this->sendResponse($userSubscriptions->toArray(), 'User Subscriptions retrieved successfully');
    }

    /**
     * @param CreateUserSubscriptionAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/user-subscriptions",
     *      summary="Store a newly created UserSubscription in storage",
     *      tags={"UserSubscription"},
     *      description="Store UserSubscription",
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
     *          description="UserSubscription that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserSubscription")
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
     *                  ref="#/definitions/UserSubscription"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */

    public function store(CreateUserSubscriptionAPIRequest $request)
    {
        try {
            $user = \Auth::user();
            // $transaction_reference = $request->input('original_transaction_id', null);
            // $transaction_reference = $transaction_reference ?? $request->input('transaction_id', null);
            $original_transaction_id = $request->input('original_transaction_id', null);
            $transaction_reference = $original_transaction_id ?? $request->input('transaction_id', null);
            $input = $request->only(['transaction_receipt', 'platform', 'product_id', 'data']);

            if(!$user->details){
                throw new Error('User details are not found!');
            }

            $userSubscription = null;
            if (!$original_transaction_id) {
                $userSubscription = UserSubscription::create([
                    'user_id'               => $user->id,
                    'reference_key'         => $transaction_reference, // $receipt['original_transaction_id'],
                    'product_id'            => $input['product_id'],
                    'platform'              => $input['platform'] ?? 'ios',
                    'transaction_receipt'   => $input['transaction_receipt'] ?? null,
                    'status'                => UserSubscription::STATUS_HOLD,
                    'data'                  => isset($input['data']) ? json_encode($input['data']) : null, // json_encode($receipt),
                ]);
                $userSubscription = $userSubscription->toArray();
            }

            unset($request['transaction_receipt']);
            Util::MakeRequestLogs('USER_SUBSCRIPTION::STORE | API::POST', $request, $userSubscription);
            return $this->sendResponse($userSubscription, 'Subscribed successfully');

        } catch (\Exception $exception) {
            return $this->sendErrorWithData([$exception->getMessage()]);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/user-subscriptions/{id}",
     *      summary="Display the specified UserSubscription",
     *      tags={"UserSubscription"},
     *      description="Get UserSubscription",
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
     *          description="id of UserSubscription",
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
     *                  ref="#/definitions/UserSubscription"
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
        /** @var UserSubscription $userSubscription */
        $userSubscription = $this->userSubscriptionRepository->findWithoutFail($id);

        if (empty($userSubscription)) {
            return $this->sendError('User Subscription not found');
        }

        return $this->sendResponse($userSubscription->toArray(), 'User Subscription retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateUserSubscriptionAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/user-subscriptions/{id}",
     *      summary="Update the specified UserSubscription in storage",
     *      tags={"UserSubscription"},
     *      description="Update UserSubscription",
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
     *          description="id of UserSubscription",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="UserSubscription that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/UserSubscription")
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
     *                  ref="#/definitions/UserSubscription"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateUserSubscriptionAPIRequest $request)
    {
        /** @var UserSubscription $userSubscription */
        $userSubscription = $this->userSubscriptionRepository->findWithoutFail($id);

        if (empty($userSubscription)) {
            return $this->sendError('User Subscription not found');
        }

        $userSubscription = $this->userSubscriptionRepository->updateRecord($request, $id);

        return $this->sendResponse($userSubscription->toArray(), 'UserSubscription updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/user-subscriptions/{id}",
     *      summary="Remove the specified UserSubscription from storage",
     *      tags={"UserSubscription"},
     *      description="Delete UserSubscription",
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
     *          description="id of UserSubscription",
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
        /** @var UserSubscription $userSubscription */
        $userSubscription = $this->userSubscriptionRepository->findWithoutFail($id);

        if (empty($userSubscription)) {
            return $this->sendError('User Subscription not found');
        }

        $this->userSubscriptionRepository->deleteRecord($id);

        return $this->sendResponse($id, 'User Subscription deleted successfully');
    }

    public function updateSubscriptionWebhook(Request $request)
    {
        Log::info('endpoint: /api/v1/user-subscription-webhook');

        $token = $request->input('signedPayload', null);
        $userId = null;
        $requestData = $request->all();
        $fcmResponse = null;
        $webhookLog = null;
        $webhookLogData = null;

        try {
            DB::beginTransaction();
            
            if ($token) {
                $decode           = Util::JWTDecodeInfo($token);
                $transactionInfo  = Util::JWTDecodeInfo($decode->data->signedTransactionInfo);
                $renewalInfo      = Util::JWTDecodeInfo($decode->data->signedRenewalInfo);
                $notificationType = $decode->notificationType;
                $subType          = isset($decode->subtype) ? $decode->subtype : null;
                // $notificationRealType = $subType ?? $notificationType;

                $expireAt = Carbon::createFromTimestamp($transactionInfo->expiresDate / 1000);
                $offerDiscountType = isset($transactionInfo->offerDiscountType) ? $transactionInfo->offerDiscountType : null;
                $transactionInfo->price = $transactionInfo->price / 1000;

                $requestData['decode'] = collect($decode);
                $requestData['transactionInfo'] = collect($transactionInfo);
                $requestData['renewalInfo'] = collect($renewalInfo);

                $decodedData = collect($decode->data);
                unset($decodedData['signedTransactionInfo']);
                unset($decodedData['signedRenewalInfo']);
                unset($requestData['signedPayload']);
                $requestData['decode']['data'] = $decodedData;

                $webhookLogData = [                    
                    'transactionId' => $transactionInfo->transactionId,
                    'originalTransactionId' => $transactionInfo->originalTransactionId,
                    'notificationType' => $notificationType,
                    'subType' => $subType,
                    'productId' => $transactionInfo->productId,
                    'expiresDate' => $transactionInfo->expiresDate,
                    'type' => $transactionInfo->type,
                    'offerDiscountType' => $offerDiscountType,

                    'platform'      => 'ios',
                    'signedPayload' => $request->input('signedPayload', null),
                    'data'          => $requestData,
                ];

                $userRecentSubscription = app(UsersubscriptionRepository::class)
                    ->where('reference_key', $transactionInfo->originalTransactionId)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $userId = $userRecentSubscription->user_id ?? null;
                if(!$userId)
                    throw new Error('User id not found | reference_key: '. $transactionInfo->originalTransactionId ?? null);

                $user = app(UserRepository::class)->model()::find($userId);
                if(!$user)
                    throw new Error('User not found');

                $userRecentActiveSubscription = app(UsersubscriptionRepository::class)->where([
                        'reference_key' => $transactionInfo->originalTransactionId,
                        'status' => in_array($notificationType, [Usersubscription::IOS_SUBSCRIBED, Usersubscription::IOS_DID_CHANGE_RENEWAL_PREF]) ? UserSubscription::STATUS_HOLD : UserSubscription::STATUS_ACTIVE
                    ])
                    // ->whereNotIn('status', [Usersubscription::STATUS_EXPIRE])
                    ->orderBy('created_at', 'desc')
                    ->first();

                if(!$userRecentActiveSubscription && $subType !== UserSubscription::IOS_SUBTYPE_RESUBSCRIBE)
                    throw new Error('User Subscription Record not found');

                $userDetails = $user->details;
                $userPushNotification = $userDetails->push_notifications ?? null;
                $description = null;

                $userNewSubscriptionData = [
                    // 'is_free_trial' => $isFreeTrail,
                    'reference_key' => $transactionInfo->originalTransactionId,
                    'status' => Usersubscription::STATUS_ACTIVE,
                    'expiry_date' => $expireAt,
                    'product_id' => $transactionInfo->productId,
                    'user_id' => $userId,
                    'currency' => $transactionInfo->currency,
                    'amount' => $transactionInfo->price,
                    'platform' => $userRecentActiveSubscription->platform ?? 'ios',
                    'is_free_trial' => 0
                ];

                // dd($notificationType, $subType, $offerDiscountType, $userId, $transactionInfo->originalTransactionId);

                switch ($notificationType) {
                    case Usersubscription::IOS_SUBSCRIBED:
                        $description = 'Purchase Subscription ('.$transactionInfo->productId.')';

                        if($subType == UserSubscription::IOS_SUBTYPE_INITIAL_BUY){

                            if($offerDiscountType == Usersubscription::FREE_TRIAL){

                                if($userDetails){
                                    // $userDetails->free_trial_expiry = $expireAt;
                                    // $userDetails->is_free_trial = 1;
                                    // $userDetails->is_subscribed = 1;
                                    $userDetails->is_free_trial_used = 1;
                                    $userDetails->save();
                                }

                                $userTrialSubscriptionData = $userNewSubscriptionData;
                                $userTrialSubscriptionData['is_free_trial'] = 1;
                                $userRecentActiveSubscription->update($userTrialSubscriptionData);
                                $userRecentActiveSubscription = $userRecentActiveSubscription->refresh();
                            }
                        }

                        if($subType == UserSubscription::IOS_SUBTYPE_RESUBSCRIBE){
                            if($userRecentActiveSubscription){
                                $userRecentActiveSubscription->status = Usersubscription::STATUS_CANCELLED;
                                $userRecentActiveSubscription->save();
                            }

                            $newUserSubscription = Usersubscription::create($userNewSubscriptionData);

                            if($userDetails){
                                $userDetails->is_subscribed = 1;
                                $userDetails->save();
                            }

                            if($userRecentActiveSubscription){
                                $this->makeTransaction($userId, $userRecentActiveSubscription, $transactionInfo, $description);
                            }
                        }

                        if ($userPushNotification) {
                            $message = 'Subscription Purchased Successfully';
                            $type = Notification::TYPE_SUBSCRIPTION_PURCHASED;
                            $fcmResponse = $this->sendPurchaseSubscriptionPushNotification($user, $message, $type);    
                        }
                        break;
                    case Usersubscription::IOS_DID_RENEW:
                        $newUserSubscription = $this->proceedIOSWebhookSubscription($userRecentActiveSubscription, $userNewSubscriptionData, $expireAt, $decode);

                        if($userDetails){
                            $userDetails->is_subscribed = 1;
                            $userDetails->save();
                        }

                        if($newUserSubscription){
                            $this->makeTransaction($userId, $newUserSubscription, $transactionInfo, $description);
                        }
                        
                        if ($userPushNotification) {
                            $message = 'Subscription Renewed Successfully';
                            $type = Notification::TYPE_SUBSCRIPTION_PURCHASED;
                            $fcmResponse = $this->sendPurchaseSubscriptionPushNotification($user, $message, $type);
                        }
                        break;
                    case Usersubscription::IOS_DID_CHANGE_RENEWAL_STATUS:
                        UserSubscription::where(['reference_key' => $transactionInfo->originalTransactionId, 'status' => Usersubscription::STATUS_ACTIVE])
                            ->update([
                                'status' => $subType == UserSubscription::IOS_AUTO_RENEW_ENABLED ? Usersubscription::STATUS_ACTIVE : Usersubscription::STATUS_CANCELLED,
                                'expiry_date' => $expireAt
                            ]);

                        if($subType == UserSubscription::IOS_AUTO_RENEW_ENABLED){
                            $userRecentActiveSubscription = $userRecentActiveSubscription->refresh();
                            
                            if($userDetails){
                                $userDetails->is_subscribed = 1;
                                $userDetails->save();
                            }

                            $this->makeTransaction($userId, $userRecentActiveSubscription, $transactionInfo, $description);
                            
                            $message = 'Subscription Active Successfully';
                            $type = Notification::TYPE_SUBSCRIPTION_PURCHASED;
                        }

                        if($subType == UserSubscription::IOS_AUTO_RENEW_DISABLED){
                            if($userDetails){
                                $userDetails->is_subscribed = 0;
                                $userDetails->save();
                            }
                            
                            $message = 'Subscription Cancelled Successfully';
                            $type = Notification::TYPE_SUBSCRIPTION_PURCHASED;
                        }
                        
                        if ($userPushNotification) {
                            $fcmResponse = $this->sendPurchaseSubscriptionPushNotification($user, $message, $type);
                        }
                        break;
                    case Usersubscription::IOS_DID_CHANGE_RENEWAL_PREF:
                        $newUserSubscription = $this->proceedIOSWebhookSubscription($userRecentActiveSubscription, $userNewSubscriptionData, $expireAt, $decode);

                        if($userDetails){
                            $userDetails->is_subscribed = 1;
                            $userDetails->save();
                        }

                        if($newUserSubscription){
                            $this->makeTransaction($userId, $newUserSubscription, $transactionInfo, $description);
                        }

                        if ($userPushNotification) {
                            $messageStatus = $subType == 'UPGRADE' ? 'Downgrade' : 'Upgrade';
                            $message = 'Subscription '.$messageStatus.' Successfully';
                            $type = Notification::TYPE_SUBSCRIPTION_PURCHASED;
                            $fcmResponse = $this->sendPurchaseSubscriptionPushNotification($user, $message, $type);
                        }
                        break;

                    case Usersubscription::IOS_EXPIRED:
                        app(UsersubscriptionRepository::class)->model()::where('reference_key', $transactionInfo->originalTransactionId)->update([
                            'status' => Usersubscription::STATUS_EXPIRE,
                            'expiry_date' => $expireAt
                        ]);

                        if($userDetails){
                            $userDetails->is_subscribed = 0;
                            $userDetails->save();
                        }
                        
                        if ($userPushNotification) {
                            $message = 'Subscription is Expired';
                            $type = Notification::TYPE_SUBSCRIPTION_PURCHASED;
                            $fcmResponse = $this->sendPurchaseSubscriptionPushNotification($user, $message, $type);
                        }
                        break;
                    default:
                        break;
                }

            }
            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            $webhookLog = WebhookLog::create([
                'data'      => $requestData,
                'error'     => $exception->getMessage().' | Line # '. $exception->getLine(),
                'platform'  => 'ios_exception'
            ]);

        } catch (\Error $error) {
            DB::rollBack();
            $webhookLog = WebhookLog::create([
                'data'      => $requestData,
                'error'     => $error->getMessage().' | Line # '. $error->getLine(),
                'platform' => 'ios_error'
            ]);
        } finally {
            if($webhookLogData){
                $webhookLogData['user_id'] = $userId ?? null;

                if($webhookLog){
                    unset($webhookLogData['platform']);
                    $webhookLog->update($webhookLogData);
                } else {
                    WebhookLog::create($webhookLogData);
                }
            }
            unset($request['signedPayload']);
            Util::MakeRequestLogs('USER_SUBSCRIPTION::WEBHOOK | API::POST', $request, null, $webhookLogData);
            return $this->sendResponse($fcmResponse, 'Result');
        }
    }

    public function sendPurchaseSubscriptionPushNotification($user, $message, $type)
    {
        $helperInstance = new NotificationsHelper();
        $fcmResponse = $helperInstance->sendPushNotificationsMessage($message, $type, $user);
        return $fcmResponse;
    }

    public function updateSubscriptionWebhookAndroid()
    {
        AndroidUserSubscriptionWebHook::dispatchNow();
    }

    public function proceedIOSWebhookSubscription($userRecentActiveSubscription, $userNewSubscriptionData, $expireAt, $decode)
    {
        if(isset($userRecentActiveSubscription)){
            if($userRecentActiveSubscription->product_id == $userNewSubscriptionData['product_id'] && !$userRecentActiveSubscription->is_free_trial){
                $userRecentActiveSubscription->update($userNewSubscriptionData);
                return $userRecentActiveSubscription->refresh();
            }
            // else {
            if($userRecentActiveSubscription->product_id != $userNewSubscriptionData['product_id'] || $userRecentActiveSubscription->is_free_trial){

                $userRecentActiveSubscription->status = Usersubscription::STATUS_CANCELLED;
                $userRecentActiveSubscription->save();

                return Usersubscription::create($userNewSubscriptionData);
            }
        }
        return $userRecentActiveSubscription;
    }
    public function makeTransaction($user_id, $newUserSubscription, $transactionInfo, $description)
    {
        $data = [
            'user_id'        => $user_id,
            'order_id'       => $newUserSubscription->id,
            'transaction_id' => $transactionInfo->originalTransactionId,
            'currency'       => $transactionInfo->currency ?? 'USD',
            'amount'         => $transactionInfo->price ?? 0,
            'type'           => Transaction::SUBSCRIPTION,
            // 'description'    => 'Subscription Payment',
            'payment_mode'   => Transaction::DEBIT,
            'description'    => $description ?? 'Purchase Subscription',
            'status'         => 'succeeded'
        ];
        app(TransactionRepository::class)->saveRecord($data);
    }

    // public function saveUserSubscription($user_id, $transactionInfo, $user_subscription, $freeTrialExpireAt, $data)
    // {
    //     $input['user_id']       = $user_id;
    //     $input['product_id']    = $transactionInfo->productId;
    //     $input['reference_key'] = $transactionInfo->originalTransactionId;
    //     $input['expiry_date']   = $freeTrialExpireAt;
    //     $input['amount']        = $transactionInfo->price;
    //     $input['currency']      = $transactionInfo->currency ?? 'USD';
    //     $input['status']        = Usersubscription::STATUS_ACTIVE;
    //     $input['platform']      = $user_subscription->platform;
    //     $input['data']          = json_encode($data);
    //     $newUserSubscription = app(UsersubscriptionRepository::class)->saveRecord($input);

    //     $user_subscription->status = Usersubscription::STATUS_CANCELLED;
    //     $user_subscription->save();

    //     $data = [
    //         'user_id'        => $user_id,
    //         'order_id'       => $newUserSubscription->id,
    //         'transaction_id' => $transactionInfo->originalTransactionId,
    //         'currency'       => $transactionInfo->currency ?? 'USD',
    //         'amount'         => $transactionInfo->price ?? 0,
    //         'type'           => Transaction::SUBSCRIPTION,
    //         'description'    => 'Subscription Payment',
    //         'payment_mode'   => Transaction::DEBIT,
    //         'description'    => 'Purchase Subscription',
    //         'status'         => 'succeeded'
    //     ];
    //     app(TransactionRepository::class)->saveRecord($data);
    // }

}
