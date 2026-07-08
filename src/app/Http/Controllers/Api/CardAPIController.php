<?php

namespace App\Http\Controllers\Api;

use App\Criteria\CardCriteria;
use App\Http\Controllers\StripePaymentController;
use App\Http\Requests\Api\CreateCardAPIRequest;
use App\Http\Requests\Api\UpdateCardAPIRequest;
use App\Models\Card;
use App\Repositories\Admin\CardRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class CardController
 * @package App\Http\Controllers\Api
 */
class CardAPIController extends AppBaseController
{
    /** @var  CardRepository */
    private $cardRepository;

    public function __construct(CardRepository $cardRepo)
    {
        $this->cardRepository = $cardRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/cards",
     *      summary="Get a listing of the Cards.",
     *      tags={"Card"},
     *      description="Get all Cards",
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
     *                  @SWG\Items(ref="#/definitions/Card")
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
        $this->cardRepository->pushCriteria(new RequestCriteria($request));
        $this->cardRepository->pushCriteria(new LimitOffsetCriteria($request));
        $cards = $this->cardRepository->pushCriteria(new CardCriteria([
            'user_id' => \Auth::id(),

        ]))
            ->paginate($limit);


        return $this->sendResponse($cards->toArray(), 'Cards retrieved successfully');
    }

    /**
     * @param CreateCardAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/cards",
     *      summary="Store a newly created Card in storage",
     *      tags={"Card"},
     *      description="Store Card",
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
     *          description="Card that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Card")
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
     *                  ref="#/definitions/Card"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCardAPIRequest $request)
    {
        try {
            DB::beginTransaction();
            $paymentMethod = app(StripePaymentController::class)->addStripeAccount($request->payment_method);

//            dd($paymentMethod->card->last4);


            $data = [
                'payment_method' => $paymentMethod->id,
                'user_id'        => \Auth::id(),
                'last_four'      => $paymentMethod->card->last4,
                'country'        => $paymentMethod->card->country,
                'brand'          => $paymentMethod->card->brand,
                'exp_year'       => $paymentMethod->card->exp_year,
                "exp_month"      => $paymentMethod->card->exp_month
            ];
//            dd(\Auth::id());

            $cards = $this->cardRepository->saveRecord($data);
            DB::commit();
            return $this->sendResponse($cards->toArray(), 'Card saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendErrorWithData([$exception->getMessage()]);
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/cards/{id}",
     *      summary="Display the specified Card",
     *      tags={"Card"},
     *      description="Get Card",
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
     *          description="id of Card",
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
     *                  ref="#/definitions/Card"
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
        /** @var Card $card */
        $card = $this->cardRepository->findWithoutFail($id);

        if (empty($card)) {
            return $this->sendError('Card not found');
        }

        return $this->sendResponse($card->toArray(), 'Card retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCardAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/cards/{id}",
     *      summary="Update the specified Card in storage",
     *      tags={"Card"},
     *      description="Update Card",
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
     *          description="id of Card",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Card that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Card")
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
     *                  ref="#/definitions/Card"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCardAPIRequest $request)
    {
        /** @var Card $card */
//        $request['user_id']=\Auth::id();
        $card = $this->cardRepository->findWithoutFail($id);

        if (empty($card)) {
            return $this->sendError('Card not found');
        }

        $card = $this->cardRepository->updateRecord($request, $id);

        return $this->sendResponse($card->toArray(), 'Card updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/cards/{id}",
     *      summary="Remove the specified Card from storage",
     *      tags={"Card"},
     *      description="Delete Card",
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
     *          description="id of Card",
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
        /** @var Card $card */
        $card = $this->cardRepository->findWithoutFail($id);

        if (empty($card)) {
            return $this->sendError('Card not found');
        }

        $this->cardRepository->deleteRecord($id);

        return $this->sendResponse($id, 'Card deleted successfully');
    }
}
