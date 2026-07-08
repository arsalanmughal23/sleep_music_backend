<?php

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: access-control-allow-origin,content-type,authorization");

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/aws-resize/{img}', function ($img) {
    ob_end_clean();
    try {
        $w      = request()->get('w');
        $h      = request()->get('h');
        $crop   = request()->get('crop', false);
        $method = ($crop) ? "fit" : "resize";
        if ($h && $w) {
            // Image Handler lib
            return Image::make(config('constants.aws_url') . $img)->$method($w, $h, function ($c) {
                $c->upsize();
                $c->aspectRatio();
            })->response('png');
        } else {
            return response(file_get_contents(config('constants.aws_url') . $img))
                ->header('Content-Type', 'image/png');
        }

    } catch (\Exception $e) {
        return abort(404, $e->getMessage());
    }
})->name('aws-resize')->where('img', '(.*)');

// Images Resize Route
Route::get('/resize/{img}', function ($img) {
    if (ob_get_contents()) {
        ob_end_clean();
    }
    try {
        $w = request()->get('w');
        $h = request()->get('h');
        if ($h && $w) {
            // Image Handler lib
            return Image::make(\Storage::url($img))->resize($h, $w, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            })->response('png');
        } else {
            return response(\Storage::get("$img"))
                ->header('Content-Type', 'image/png');
        }

    } catch (\Exception $e) {
//        dd($e->getMessage());
        return abort(404, $e->getMessage());
    }
})->name('resize')->where('img', '(.*)');


/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

## No Token Required
Route::post('v1/register', 'AuthAPIController@register')->name('register');
Route::post('v1/login', 'AuthAPIController@login')->name('login');
Route::post('v1/resend-otp', 'AuthAPIController@resendOTPCode');
Route::post('v1/testing_email', 'AuthAPIController@testEmail');

Route::post('v1/verify-email-otp', 'AuthAPIController@verifyUserEmailVerificationOTPCode');
Route::get('v1/forget-password', 'AuthAPIController@forgetPassword')->name('forget-password');
Route::post('v1/verify-reset-password-otp', 'AuthAPIController@verifyUserPasswordOTPCode')->name('verify-code');
Route::post('v1/reset-password', 'AuthAPIController@resetPassword')->name('reset-password');


Route::post('v1/social_login', 'AuthAPIController@socialLogin')->name('socialLogin');

Route::resource('v1/categories', 'CategoryAPIController')->only('index', 'show');


// Fixme: Temporary solution .
Route::post('v1/refresh', 'AuthAPIController@refresh');
Route::resource('v1/media', 'MediaAPIController')->only('index');

Route::middleware('auth:api')->group(function () {
    Route::get('v1/categories_with_sounds', 'CategoryAPIController@categoriesWithSounds');

    Route::get('v1/me', 'AuthAPIController@me');
    Route::post('v1/change-password', 'AuthAPIController@changePassword');
    Route::post('v1/delete-account', 'UserAPIController@deleteAccount');
    Route::resource('v1/users', 'UserAPIController');
    Route::post('v1/update-profile', 'UserAPIController@update_user');
    Route::put('v1/push-notification', 'UserAPIController@push_notification');
    Route::post('v1/logout', 'AuthAPIController@logout');

    Route::resource('v1/reports', 'ReportAPIController');
    Route::resource('v1/report-types', 'ReportTypeAPIController');
    Route::resource('v1/delete-types', 'DeleteTypeAPIController');
    Route::resource('v1/media', 'MediaAPIController')->except('index');
    Route::resource('v1/mixer', 'MixerAPIController')->only('index', 'store', 'update', 'destroy');
    Route::resource('v1/app-ratings', 'AppRatingAPIController');


    Route::resource('v1/transactions', 'TransactionAPIController')->only('index');
    Route::resource('v1/user-subscriptions', 'UserSubscriptionAPIController')->only('index', 'store');

    Route::resource('v1/webhook-logs', 'WebhookLogAPIController');
    
    Route::post('v1/clear-subscription', function(Request $request){
        $user = $request->user();
        $userDetail = $user->details;

        if($userDetail){
            // $userDetail->free_trial_expiry = null;
            // $userDetail->is_free_trial = 0;
            $userDetail->is_free_trial_used = 0;
            $userDetail->is_subscribed = 0;
            $userDetail->save();
        }

        \App\Models\UserSubscription::where('user_id', $user->id)->delete();
        \App\Models\Transaction::where('user_id', $user->id)->delete();
        \App\Models\WebhookLog::query()->delete();
        return $user->refresh();
    });
});

Route::get('v1/pages', 'PageAPIController@index');
Route::get('v1/pages/{page}', 'PageAPIController@show');


Route::post('v1/user-subscription-webhook', 'UserSubscriptionAPIController@updateSubscriptionWebhook');
Route::get('v1/user-subscription-webhook-android', 'UserSubscriptionAPIController@updateSubscriptionWebhookAndroid');


Route::resource('v1/packages', 'PackageAPIController');