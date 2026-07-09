<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Repositories\Admin\UserDetailRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function returnConnectAccount(Request $request)
    {
        $input = $request->all();
        if (isset($input['user_id']) && isset($input['account_id'])) {
            $account = app(StripePaymentController::class)->getAccountInfo($input['account_id']);
            if ($account['details_submitted']) {
                app(UserDetailRepository::class)->where('user_id', $input['user_id'])->update([
                    'connect_account_id' => $input['account_id']
                ]);
            }
        }

        return view('payment-success');
    }

    public function returnConnectAccountFailure(Request $request)
    {
        $input = $request->all();
        if (isset($input['user_id'])) {
            app(UserDetailRepository::class)->where('user_id', $input['user_id'])->update([
                'connect_account_id' => null
            ]);
        }

        return view('payment-failure');
    }

    public function getUrl($id)
    {
        $media = Media::where('id', $id)->first();
        return view('url2')->with(['media' => $media]);
    }

    public function GetPages($slug)
    {
        $page = Page::where('slug', $slug)->where('status', 1)->first();
        return view('pages')->with(['page' => $page]);

    }
}
