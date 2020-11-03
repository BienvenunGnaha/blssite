<?php

use Illuminate\Http\Request;


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

Route::post('/stripe/subscription/renew', 'HomeController@renew')->name('stripe-renew');
Route::post('/stripe/setupintent', 'Api\StripeController@setupIntent');
Route::get('/home', 'Api\SiteController@home');

Route::group([
    'middleware' => ['api',],
    'prefix' => 'auth'

], function ($router) {
    
    Route::post('login', 'Api\AuthController@login');
    Route::post('register', 'Api\AuthController@register');
    
    Route::post('refresh/token', 'Api\AuthController@refresh');
});

Route::group([
    'middleware' => [],
    'prefix' => 'user'

], function ($router) {
    Route::post('reset-pw-link', 'Api\AuthController@sendResetLinkEmail');
    Route::post('verifycode', 'Api\AuthController@verifyCode');
    Route::post('reset-pw', 'Api\AuthController@resetpw');
});



Route::group([
    'middleware' => ['api', 'jwt.verify'],
    'prefix' => 'auth'

], function ($router) {
    
    Route::post('logout', 'Api\AuthController@logout');
    Route::get('user-profile', 'Api\AuthController@userProfile');
});

Route::group([
    'middleware' => ['api', 'jwt.verify'],
    'prefix' => 'user'

], function ($router) {
    
    Route::get('dashboard', 'Api\UserController@home');
    Route::get('plans', 'Api\SiteController@plans');
    Route::get('pronostics', 'Api\SiteController@pronostics');
    Route::get('matrix/{lv_no}', 'Api\SiteController@matrixIndex');
    Route::get('referals', 'Api\UserController@referralIndex');
    Route::get('transactions', 'Api\UserController@transactions');
    Route::get('2fa', 'Api\UserController@show2Fa');
    Route::get('login-history', 'Api\UserController@loginHistory');
    Route::get('profile', 'Api\UserController@userProfile');
    Route::get('withdrawals', 'Api\UserController@withdrawHistory');
    Route::get('levelCommisionTrx', 'Api\UserController@levelCommisionTrx');
    Route::post('edit', 'Api\UserController@profileUpdate');
    Route::post('edit/password', 'Api\UserController@passwordUpdate');//plan/subscribe
    Route::post('enable2fa', 'Api\UserController@create2fa');
    Route::post('disable2fa', 'Api\UserController@disable2fa');
    Route::post('verify2fa', 'Api\UserController@verify2fa');
    Route::post('stripe/setupintent', 'Api\StripeController@userSetupIntent');
    Route::post('plan/subscribe', 'Api\SiteController@planStore');
});


