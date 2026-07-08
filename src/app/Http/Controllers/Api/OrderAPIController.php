<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\StripePaymentController;
use App\Http\Requests\Api\CreateOrderAPIRequest;
use App\Http\Requests\Api\CreatePaypalOrderAPIRequest;
use App\Http\Requests\Api\UpdateOrderAPIRequest;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\UserDetail;
use App\Repositories\Admin\CardRepository;
use App\Repositories\Admin\OrderRepository;
use App\Repositories\Admin\SettingRepository;
use App\Repositories\Admin\TransactionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;


/**
 * Class OrderController
 * @package App\Http\Controllers\Api
 */
class OrderAPIController extends AppBaseController
{
    /** @var  OrderRepository */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepo)
    {
        $this->orderRepository = $orderRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/orders",
     *      summary="Get a listing of the Orders.",
     *      tags={"Order"},
     *      description="Get all Orders",
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
     *                  @SWG\Items(ref="#/definitions/Order")
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
        $this->orderRepository->pushCriteria(new RequestCriteria($request));
        $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
        $orders = $this->orderRepository->all();

        return $this->sendResponse($orders->toArray(), 'Orders retrieved successfully');
    }

    /**
     * @param CreateOrderAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/orders",
     *      summary="Store a newly created Order in storage",
     *      tags={"Order"},
     *      description="Store Order",
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
     *          description="Order that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Order")
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
     *                  ref="#/definitions/Order"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateOrderAPIRequest $request)
    {
//        $request['card'] = 'visa';
        try {
            DB::beginTransaction();
            try {
                $ordersdetail = $this->orderRepository->saveRecord($request);

            } catch (\Exception $exception) {
                if ($exception->getCode() == 23000) {
                    return $this->sendErrorWithData(["Card not found"]);
                }
                return $this->sendErrorWithData([$exception->getMessage()]);
            }


            $total_amount = $request->total_amount;

//            if (isset($request->is_native) && $request->is_native == 1) {
//                $total_amount = round($total_amount, 2);
//                $charge       = app(StripePaymentController::class)->chargeThroughToken($total_amount, $request->stripe_token);
//            } else {
//
            $set          = Setting::first();
            $setting      = app(SettingRepository::class)->findWithoutFail($set->id);
            $commission   = ($setting->commission / 100);
            $adminpayable = $commission * $total_amount;
            $adminpayable = round($adminpayable, 2);
            $paytoartist  = $total_amount - $adminpayable;
            $paytoartist  = round($paytoartist, 2);
            if (isset($request->is_native) && $request->is_native == 1) {

                $charge = app(StripePaymentController::class)->chargeCardWithToken($request);

//                $charge = app(StripePaymentController::class)->createCharge($total_amount, $request->stripe_token);
            } else {
                $card   = app(CardRepository::class)->findWithoutFail($request->card_id);
                $charge = app(StripePaymentController::class)->chargeCard($total_amount, $card->payment_method);

            }

//            $charge       = app(StripePaymentController::class)->chargeCard($total_amount, $card->payment_method);
//            dd($charge['status']);
            if ($charge['status'] != 'succeeded') {
                throw new \Exception('unable to perform payment');
            }
            $pay_to_user = UserDetail::where('user_id', $ordersdetail->donated_to_id)->first();
            if ($pay_to_user->connect_account_id != null) {
                $charge2 = app(StripePaymentController::class)->payout($paytoartist, $pay_to_user->connect_account_id);
////                dd($charge);
//                if ($charge['status'] != 'succeeded') {
//                    throw new \Exception('unable to perform payment');
//                }
            }

            $data = [
                'user_id'       => \Auth::id(),
                'order_id'      => $ordersdetail->id,
                'donated_to_id' => $ordersdetail->donated_to_id,
                'currency'      => $charge['currency'],
                'pay_to_artist' => $paytoartist,
                'amount'        => $charge['amount'] / 100,
                'text'          => $request->text,
                'description'   => 'Donation to artist #ID-' . $ordersdetail->donated_to_id . ' $' . $paytoartist . ' admin commission $' . $adminpayable . ' and left this text "' . $request->text . '"',
                'status'        => $charge['status'],
                'type'          => Transaction::PAY_TYPE_STRIPE
            ];


            app(TransactionRepository::class)->saveRecord($data);


            DB::commit();
            $notification = Notification::create([
                'sender_id'   => \Auth::id(),
                'action_type' => Notification::DONATION,
                'message'     => '[name] Donated $' . $paytoartist . ' and left this message "' . $request->text . '"',
                'status'      => 1
            ]);

            NotificationUser::create([
                'notification_id' => $notification->id,
                'user_id'         => (int)$ordersdetail->donated_to_id,
                'status'          => 1
            ]);
            return $this->sendResponse($charge, 'Donation successfully Done');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorWithData([$e->getMessage()]);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/orders/{id}",
     *      summary="Display the specified Order",
     *      tags={"Order"},
     *      description="Get Order",
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
     *          description="id of Order",
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
     *                  ref="#/definitions/Order"
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
        /** @var Order $order */
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError('Order not found');
        }

        return $this->sendResponse($order->toArray(), 'Order retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateOrderAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/orders/{id}",
     *      summary="Update the specified Order in storage",
     *      tags={"Order"},
     *      description="Update Order",
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
     *          description="id of Order",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Order that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Order")
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
     *                  ref="#/definitions/Order"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateOrderAPIRequest $request)
    {
        /** @var Order $order */
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError('Order not found');
        }

        $order = $this->orderRepository->updateRecord($request, $id);

        return $this->sendResponse($order->toArray(), 'Order updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/orders/{id}",
     *      summary="Remove the specified Order from storage",
     *      tags={"Order"},
     *      description="Delete Order",
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
     *          description="id of Order",
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
        /** @var Order $order */
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError('Order not found');
        }

        $this->orderRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Order deleted successfully');
    }

    public function BriantreeToken()
    {
        $gateway = new \Braintree\Gateway([
            'environment' => env('BTREE_ENVIRONMENT'),
            'merchantId'  => env('BTREE_MERCHANT_ID'),
            'publicKey'   => env('BTREE_PUBLIC_KEY'),
            'privateKey'  => env('BTREE_PRIVATE_KEY')
        ]);
//        return $clientToken = $gateway->clientToken()->generate([
//            "customerId" => $aCustomerId
//        ]);

        return $this->sendResponse(["token" => $gateway->clientToken()->generate()], 'BrainTree Token retrieved successfully');
    }

    public function BrainTreeCharge(CreatePaypalOrderAPIRequest $request)
    {
        try {
            DB::beginTransaction();
            try {
                $ordersdetail = $this->orderRepository->saveRecord($request);

            } catch (\Exception $exception) {
                if ($exception->getCode() == 23000) {
                    return $this->sendErrorWithData(["Card not found"]);
                }
                return $this->sendErrorWithData([$exception->getMessage()]);
            }


            $total_amount = $request->total_amount;

            $set          = Setting::first();
            $setting      = app(SettingRepository::class)->findWithoutFail($set->id);
            $commission   = ($setting->commission / 100);
            $adminpayable = $commission * $total_amount;
            $adminpayable = round($adminpayable, 2);
            $paytoartist  = $total_amount - $adminpayable;
            $paytoartist  = round($paytoartist, 2);

//                $charge = app(StripePaymentController::class)->chargeCard($total_amount, $card->payment_method);
            $gateway = new \Braintree\Gateway([
                'environment' => env('BTREE_ENVIRONMENT'),
                'merchantId'  => env('BTREE_MERCHANT_ID'),
                'publicKey'   => env('BTREE_PUBLIC_KEY'),
                'privateKey'  => env('BTREE_PRIVATE_KEY')
            ]);
            $result  = $gateway->transaction()->sale([
                'amount'             => $total_amount,
                'paymentMethodNonce' => $request->nonce,
                'options'            => [
                    'submitForSettlement' => True
                ]
            ]);

            if (!$result->success) {

                throw new \Exception('unable to perform payment');
            }
            $pay_to_user = UserDetail::where('user_id', $ordersdetail->donated_to_id)->first();
            if ($pay_to_user->connect_account_id != null) {
                $charge2 = app(StripePaymentController::class)->payout($paytoartist, $pay_to_user->connect_account_id);

//                if ($charge2['status'] != 'succeeded') {
//                    throw new \Exception('unable to perform payment');
//                }
            }

            $data = [
                'user_id'       => \Auth::id(),
                'order_id'      => $ordersdetail->id,
                'donated_to_id' => $ordersdetail->donated_to_id,
                'currency'      => $result->transaction->currencyIsoCode,
                'pay_to_artist' => $paytoartist,
                'amount'        => $result->transaction->amount,
                'text'          => $request->text,
                'description'   => 'Donation to artist #ID-' . $ordersdetail->donated_to_id . ' $' . $paytoartist . ' admin commission $' . $adminpayable . ' and left this message "' . $request->text . '"',
                'status'        => 'succeeded',
                'type'          => Transaction::PAY_TYPE_PAYPAL
            ];


            app(TransactionRepository::class)->saveRecord($data);


            DB::commit();
            $notification = Notification::create([
                'sender_id'   => \Auth::id(),
                'action_type' => Notification::DONATION,
                'message'     => '[name] Donated $' . $paytoartist . ' and left this message "' . $request->text . '"',
                'status'      => 1
            ]);

            NotificationUser::create([
                'notification_id' => $notification->id,
                'user_id'         => (int)$ordersdetail->donated_to_id,
                'status'          => 1
            ]);
            return $this->sendResponse($result, 'Donation successfully Done');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorWithData([$e->getMessage()]);
        }
//        $gateway = new \Braintree\Gateway([
//            'environment' => 'sandbox',
//            'merchantId'  => 'kpctkdwv79rdt8jd',
//            'publicKey'   => 'xgc823js88t2n585',
//            'privateKey'  => '23caa2d5ccb6013718e923a9399d61d3'
//        ]);

//        return $this->sendResponse($result, 'Order updated successfully');
    }
}
