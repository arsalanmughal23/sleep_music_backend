<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddStripeAccount;
use App\Repositories\Admin\UserCardRepository;
use App\Repositories\Admin\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use PHPUnit\Framework\Exception;
use Stripe;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\Api\TestPush;

class StripePaymentController extends AppBaseController
{
    public function chargeCardWithToken($request)
    {
        $input = is_array($request) ? $request : $request->all();
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount'               => $input['total_amount'] * 100,
            'currency'             => 'usd',
            'payment_method_types' => ['card'],
            'payment_method'       => $input['stripe_token']
        ]);
        $paymentMethod = $this->conformPI($paymentIntent->id, $input['stripe_token']);
        return $paymentMethod->toArray();
    }

    public function conformPI($paymentIntent, $payment_method_id)
    {
        $stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );
        return $stripe->paymentIntents->confirm(
            $paymentIntent,
            ['payment_method' => $payment_method_id]
        );
    }

    public function createPaymentIntent($amount, $payment_method_id)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = \Stripe\PaymentIntent::create([
            'customer'             => \Auth::user()->details->stripe_customer_id,
            'amount'               => $amount * 100,
            'currency'             => 'usd',
            'payment_method_types' => ['card'],
            'payment_method'       => $payment_method_id
        ]);
        return $paymentIntent->toArray();
    }

    public function chargeCard($amount, $payment_method_id)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = \Stripe\PaymentIntent::create([
            'customer'             => \Auth::user()->details->stripe_customer_id,
            'amount'               => $amount * 100,
            'currency'             => 'usd',
            'payment_method_types' => ['card'],
            'payment_method'       => $payment_method_id
        ]);
        $paymentMethod = $this->conformPI($paymentIntent->id, $payment_method_id);
        return $paymentMethod->toArray();
    }

    public function addStripeAccount($payment_method)
    {

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));


        $stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );
//        dd(\Auth::id());
        $paymentMethod = $stripe->paymentMethods->attach(
            $payment_method,
            ['customer' => \Auth::user()->details->stripe_customer_id]
        );
//        dd($paymentMethod);
        /* \Stripe\Customer::update(
             \Auth::user()->details->stripe_customer_id,
             [
                 'invoice_settings' => ['default_payment_method' => $paymentMethod->id],
             ]
         );*/

        return $paymentMethod;


    }

    public function addStripeExternalAccount(AddStripeAccount $request)
    {
        $input = $request->all();
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        if (\Auth::user()->details->external_account_id == null) {

            $account = \Stripe\Account::createExternalAccount(
                env('CLIENT_CONNECT_ACCOUNT'),
                [
                    'external_account' => $input['stripeToken'],
                ]
            );
        } else {
            $this->deleteExternalAccount(\Auth::user()->details->external_account_id);
            $account = \Stripe\Account::createExternalAccount(
                env('CLIENT_CONNECT_ACCOUNT'),
                [
                    'external_account' => $input['stripeToken'],
                ]
            );
        }

        $user                      = \Auth::user()->details;
        $user->external_account_id = $account->id;
        $user->save();
        return $account;


    }

    public function deleteCustomer($id)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $customer = \Stripe\Customer::retrieve(
            $id
        );
        $customer->delete();
        return true;
    }

    public function deleteCard($payment_method_id)
    {
        $stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );
        $stripe->paymentMethods->detach(
            $payment_method_id,
            []
        );
        return true;
    }

    public function getCustomer($id)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $customer = \Stripe\Customer::retrieve($id);
        return $customer;
    }

    public function deleteExternalAccount($id)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $deleted = \Stripe\Account::deleteExternalAccount(
            env('CLIENT_CONNECT_ACCOUNT'),
            $id
        );
        return $deleted;
    }

    public function getBalance()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $balance = \Stripe\Balance::retrieve();
        return $balance;
//        dd($balance->available[0]->amount);
    }

    public function stripePayout($amount)
    {

        $user            = \Auth::user()->details;
        $customer_wallet = $user->wallet;

        if (isset($user->external_account_id)) {

            if ($amount < $customer_wallet) {

                $balance = $this->getBalance();

                if (isset($balance->available[0]->source_types->card) && $balance->available[0]->source_types->card >= ($amount * 100)) {


                    // Create a Transfer to a connected account (later):
                    $transfer = \Stripe\Transfer::create([
                        'amount'      => $amount * 100,
                        'currency'    => 'usd',
                        'destination' => env('CLIENT_CONNECT_ACCOUNT'),
                    ]);

                    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    $payout = \Stripe\Payout::create([
                        'amount'      => $amount * 100,
                        'currency'    => 'usd',
                        'destination' => $user->external_account_id,
                        'source_type' => 'card',
                    ], ['stripe_account' => env('CLIENT_CONNECT_ACCOUNT')]);

                    return $this->sendResponse($payout, 'Paid Successfully');

                } else {
                    return $this->sendErrorWithData(['insufficent amount'], '403');
                }
            }
            return $this->sendErrorWithData(['insufficent amount'], '403');

        }

        return $this->sendErrorWithData(['User card does not exists'], '403');
    }

    public function createAccountLink()
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $account               = \Stripe\Account::create([
            'type' => 'express',
        ]);
        $account_links         = \Stripe\AccountLink::create([
            'account'     => $account->id,
            'refresh_url' => url('/return-connect-account-failure') . "?user_id=" . \Auth::id(),
            'return_url'  => url('/return-connect-account') . "?user_id=" . \Auth::id() . "&account_id=" . $account->id,
            'type'        => 'account_onboarding'
        ]);
        $data['account']       = $account;
        $data['account_links'] = $account_links;
        return $data;
    }

    public function removeConnectAccount($account_id)
    {
        $stripe        = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $removeAccount = $stripe->accounts->delete(
            $account_id,
            []
        );
        return $removeAccount;
    }

    public function createCustomer($email)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $customer = \Stripe\Customer::create([
            'email' => $email,
        ]);
        return $customer;
    }

    public function getAccountInfo($account_id)
    {
        $stripe  = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );
        $account = $stripe->accounts->retrieve(
            $account_id,
            []
        );
        return $account->toArray();
    }

    public function payout($amount, $connect_account_id)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $transfer = \Stripe\Transfer::create([
            "amount"      => $amount * 100,
            "currency"    => "usd",
            "destination" => $connect_account_id,
        ]);
        return $transfer->toArray();
    }

    public function chargeThroughToken($amount, $token)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $charge = \Stripe\Charge::create([
            'amount'   => $amount * 100,
            'currency' => 'usd',
            //          'description' => 'Example charge',
            'source'   => $token,
//            'statement_descriptor' => 'Custom descriptor',
        ]);
        return $charge->toArray();
    }


    public function createCharge($amount, $token)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $charge = $stripe->charges->create([
            'amount'   => round($amount * 100),
            'currency' => 'usd',
            'source'   => $token
        ]);
        return $charge->toArray();
    }

}