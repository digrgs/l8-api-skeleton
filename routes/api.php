<?php

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return response()->json([
        'status' => 200,
        'success' => true,
        'message' => config('app.name') . ' vs. ' . config('app.version'),
        'data' => ['Developer' => 'developer', 'Copyright' => '2020 - ' . Carbon::now()->format('Y')]
    ], 200);
});

/**
 * Auth group routes access
 */
$auth = [
    'prefix' => 'auth',
    'domain' => '',
    'middleware' => 'api',
    'as' => 'auth.',
    'namespace' => 'Auth',
];
Route::group($auth, function () {
    Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login',]);
    Route::post('logout', ['as' => 'logout', 'uses' => 'AuthController@logout',]);
    Route::post('refresh', ['as' => 'refresh', 'uses' => 'AuthController@refresh',]);
    Route::post('me', ['as' => 'me', 'uses' => 'AuthController@me',]);
}); // end group auth

/**
 * User group routes access
 */
$user = [
    'prefix' => 'users',
    'domain' => '',
    'middleware' => 'auth:api',
    'as' => 'users.',
    'namespace' => 'User',
];
Route::group($user, function () {
    Route::get('', ['as' => 'index', 'uses' => 'UserAPIController@index',]);
    Route::post('', ['as' => 'store', 'uses' => 'UserAPIController@store',]);
    Route::get('{id}', ['as' => 'show', 'uses' => 'UserAPIController@show',]);
    Route::put('{id}', ['as' => 'update', 'uses' => 'UserAPIController@update',]);
    Route::delete('{id}', ['as' => 'delete', 'uses' => 'UserAPIController@destroy',]);

    // active and deactive user
    Route::post('{id}/change_active_status', ['as' => 'change.active.status', 'uses' => 'UserAPIController@changeActiveStatus',]);
    Route::post('{id}/active', ['as' => 'active', 'uses' => 'UserAPIController@active',]);
    Route::post('{id}/deactive', ['as' => 'deactive', 'uses' => 'UserAPIController@deactive',]);

    // avatar user
    Route::get('avatar/url', ['as' => 'avatar.url', 'uses' => 'UserAPIController@getAvatarUrl']);
    Route::put('avatar/upload', ['as' => 'avatar.upload', 'uses' => 'UserAPIController@avatarUpload']);
    Route::put('avatar/delete', ['as' => 'avatar.delete', 'uses' => 'UserAPIController@avatarDelete']);
}); // end group user



Route::fallback(function () {
    return response()->json([
        'status' => 404,
        'success' => false,
        'message' => __('messages.page_not_found'),
        'data' => null
    ], 404);
});
