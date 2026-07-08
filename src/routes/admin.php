<?php

use App\Helper\NotificationsHelper;
use App\Models\Notification;
use Illuminate\Http\Request;

Auth::routes();

Route::get('/seed', function () {
    factory(\App\Models\Media::class, 50)->create();
    factory(\App\Models\Playlist::class, 50)->create();

    /*
     * ->each(function ($user) {
            $user->posts()->save(factory(App\Post::class)->make());
        })
     *
     * */
})->name('seed');

Route::get('/test_notification', function(Request $request){
    $userId = $request->get('user_id', null);
    $user = \App\Models\User::find($userId);

    if(!$user)
        return 'User not found';

    $message = 'Subscription Purchased Successfully';
    $type = Notification::TYPE_SUBSCRIPTION_PURCHASED;
    $helperInstance = new NotificationsHelper();
    $helperInstance->sendPushNotificationsMessage($message, $type, $user);
    return 'Done';
});

Route::get('/purge-cache', function () {
    if (app('cache')->clear()) {
        Flash::success('Cache purged successfully.');
        return redirect()->back();
    }
    Flash::error('Cache purged unsuccessful.');
    return redirect()->back();
})->name('purge-cache');

Route::get('/', 'HomeController@index')->name('dashboard');
Route::get('/home', 'HomeController@index')->name('dashboard');
Route::get('/dashboard', 'HomeController@index')->name('dashboard');
Route::get('about', 'HomeController@index');

Route::resource('roles', 'RoleController');

Route::resource('modules', 'ModuleController');

Route::get('/module/step1/{id?}', 'ModuleController@getStep1')->name('modules.create');
Route::get('/module/step2/{tablename?}', 'ModuleController@getStep2')->name('modules.create');
Route::get('/getJoinFields/{tablename?}', 'ModuleController@getJoinFields');
Route::get('/module/step3/{tablename?}', 'ModuleController@getStep3')->name('modules.create');

Route::post('/step1', 'ModuleController@postStep1');
Route::post('/step2', 'ModuleController@postStep2');
Route::post('/step3', 'ModuleController@postStep3');

// IMPORT STEPS
Route::get('/media/import/1', 'MediaController@importStep1')->name('import.media.1');
Route::get('/media/import/2', 'MediaController@importStep2')->name('import.media.2');

Route::post('/media/import/1', 'MediaController@postStep1')->name('import.media.1');
Route::post('/media/import/2', 'MediaController@postStep2')->name('import.media.2');

Route::resource('users', 'UserController');

Route::resource('permissions', 'PermissionController');

//Route::resource('profile', 'UserController');

Route::get('user/profile', 'UserController@profile')->name('users.profile');
//Route::patch('users/profile-update/{id}', 'UserController@profileUpdate')->name('users.profile-update');

Route::resource('languages', 'LanguageController');

Route::resource('pages', 'PageController');

Route::resource('contactus', 'ContactUsController');

Route::resource('notifications', 'NotificationController');

Route::resource('menus', 'MenuController');

//Menu #TODO need to be fixed
Route::get('statusChange/{id}', 'MenuController@statusChange');

Route::post('updateChannelPosition', 'MenuController@update_channel_position')->name('channels');

Route::resource('settings', 'SettingController');

Route::resource('categories', 'CategoryController');

Route::match(['GET', 'POST'], 'media/add-to-playlist/{media}', 'MediaController@addToPlaylist')->name('add_to_playlist.media');
Route::resource('medias', 'MediaController');

Route::resource('playlists', 'PlaylistController');

Route::resource('clients', 'ClientController');

Route::resource('client-connection-logs', 'ClientConnectionLogController');

Route::get('/playlist-categories', 'PlaylistController@categories')->name('playlist.categories');
Route::get('/playlist-playlists', 'PlaylistController@playlists')->name('playlist.playlists');

Route::resource('likes', 'LikeController');

Route::resource('comments', 'CommentController');

Route::resource('reports', 'ReportController');
Route::put('reports-status/{id}', 'ReportController@status');
Route::put('users-status/{id}', 'UserController@status');

Route::resource('follows', 'FollowController');

Route::resource('analytics', 'AnalyticController');

Route::resource('report-types', 'ReportTypeController');

Route::resource('views', 'ViewController');

Route::resource('trending-artists', 'TrendingArtistController');

Route::resource('notification-users', 'NotificationUserController');

Route::resource('mediaviews', 'MediaviewController');

Route::resource('orders', 'OrderController');

Route::resource('transactions', 'TransactionController');

Route::resource('cards', 'CardController');

Route::resource('delete-types', 'DeleteTypeController');

Route::resource('app-ratings', 'AppRatingController');

Route::resource('user-subscriptions', 'UserSubscriptionController');

Route::resource('packages', 'PackageController');