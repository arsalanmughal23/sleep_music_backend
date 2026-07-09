<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('password-update', 'Admin\Auth\ResetPasswordController@resetPassword')->name('admin.password-update');

Route::get('/', function () {
    return redirect('admin/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/category/swape', 'Admin\CategoryController@swape');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/return-connect-account', 'HomeController@returnConnectAccount');
Route::get('/get-url/{id}', 'HomeController@getUrl');
Route::get('/get-url', function () {
    return view('url2');
});
Route::get('/return-connect-account-failure', 'HomeController@returnConnectAccountFailure');
Route::get('/{slug}', 'HomeController@GetPages');
Route::get('/google-analytics', function () {
    return view('google-analytics');
});

