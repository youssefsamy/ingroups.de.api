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

Route::post('/user/register', 'Api\UserController@register');
Route::post('/user/registerInfo', 'Api\UserController@registerInfo');
Route::post('/user/login', 'Api\UserController@login');
Route::post('/user/forgotPassword', 'Api\UserController@forgotPassword');
Route::post('/user/resetPassword', 'Api\UserController@resetPassword');
Route::post('/user/sendVerifyEmail', 'Api\UserController@sendVerifyEmail');
Route::get('/user/verifyMail/{confirm_code}', 'Api\UserController@verifyMail');

Route::get('/user/loadBusinessUsers', 'Api\UserController@loadBusinessUsers');

Route::get('/event/verify_new_event/{confirm_code}', 'Api\EventController@verifyNewEvent');

Route::post('/upload/image', 'Api\UploadController@upload');

Route::post('/event/loadEvent', 'Api\EventController@loadEvent');
Route::post('/specialevent/getSpecialEvent', 'Api\SpecialEventController@getSpecialEvent');

Route::get('/specialevent/getAllLocations', 'Api\SpecialEventController@getAllLocations');
Route::get('/specialevent/getAllLocality', 'Api\SpecialEventController@getAllLocality');
Route::post('/specialevent/getLocalityById', 'Api\SpecialEventController@getLocalityById');


Route::group(['middleware' => ['jwt.auth']], function()
{
    Route::post('/event/createEvent', 'Api\EventController@createEvent');
    Route::post('/event/createContact', 'Api\EventController@createContact');
    Route::post('/event/contact2User', 'Api\EventController@contact2User');
    Route::post('/event/endEvent', 'Api\EventController@endEvent');
    Route::get('/event/getContactListByEvent', 'Api\EventController@getContactListByEvent');

    Route::get('/event/getMyEvents', 'Api\EventController@getMyEvents');
    Route::get('/account/getAccount', 'Api\AccountController@getAccount');

    Route::get('/account/getProfile', 'Api\AccountController@getProfile');
    Route::post('/account/saveProfile', 'Api\AccountController@saveProfile');
    Route::get('/account/getContactList', 'Api\AccountController@getContactList');

    Route::post('/message/sendMessage', 'Api\MessageController@sendMessage');
    Route::post('/message/loadMessage', 'Api\MessageController@loadMessage');
    Route::post('/message/readMessage', 'Api\MessageController@readMessage');    
    Route::post('/specialevent/updateTelefonnummer', 'Api\SpecialEventController@updateTelefonnummer');
    Route::post('/specialevent/updateUpperlimit', 'Api\SpecialEventController@updateUpperlimit');
});
