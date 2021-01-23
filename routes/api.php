<?php

use Illuminate\Http\Request;
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

/* Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'Auth\AuthController@login')->name('login');

    Route::post('register', 'Auth\AuthController@register');
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'Auth\AuthController@logout');
        Route::get('user', 'Auth\AuthController@user');
    });
});

Route::group([
  'middleware' => 'auth:api'
], function() {
    Route::post('check', 'ApiController@check');
    Route::post('loginCode', 'ApiController@loginCode')->name('loginCode');
    Route::post('content', 'ApiController@content');
    Route::post('register', 'ApiController@register');
    Route::post('download', 'ApiController@download');
    Route::post('update', 'ApiController@update');
});

Route::group([
    'prefix' => 'screen'
  ], function() {

    Route::post('all', 'UserController@getScreensApi');
    Route::post('get', 'UserController@getOneScreensApi');
    Route::post('addApi', 'UserController@addScreensApi');
    Route::post('delete', 'UserController@delScreensApi');
    Route::post('update', 'UserController@uptScreensApi');
     Route::post('schedule', 'playlistController@addSchedule');
    Route::post('schedule/partial', 'playlistController@addPartialSchedule');


});




Route::prefix('user')->group(function () {
  Route::post('show', 'UserController@showUserApi');
  Route::post('update', 'UserController@updateUserApi');
  Route::post('loginGoogle', 'UserController@loginGoogle');

 
});


Route::group(['middleware' => ['cors']], function () {
    Route::prefix('playlist')->group(function () {
  Route::post('All', 'UserController@getPlaylistsApi');
  Route::post('update', 'UserController@updatePlaylistApi');
  Route::post('playlistContent', 'UserController@viewPlaylistApi');
  Route::post('playlistContentAll', 'UserController@viewPlaylistAllApi');
  Route::post('playlistScreen', 'UserController@viewPlaylistScreenApi');
  Route::post('playlistCreate', 'PlaylistController@createPlayListApi');
  Route::post('BorrarContenido', 'UserController@delPlaylistApi');
  Route::post('TiempoCompleto', 'UserController@addScheduleApi');
  Route::post('BorrarSchedule', 'UserController@delScheduleApi');
  Route::post('BorrarPlaylist', 'UserController@deletePlaylistApi');
  Route::post('asignarContentplaylist', 'UserController@asignarContentPlaylistApi');
  Route::post('setPlaylistContentApi', 'PlaylistController@setPlaylistContentApi');
  Route::post('guardarVideo', 'UserController@guardarVideo');
  Route::post('setContentApi', 'UserController@setContentApi');
  Route::post('crearMiniatura', 'UserController@crearMiniatura');
  // Route::post('/', 'PlaylistController@addPlaylist');
  // Route::delete('/', 'PlaylistController@delPlaylist');
  
  // Route::get('/{id}', 'PlaylistController@viewPlaylist')->name('playlist_details');
  // 

  // Route::post('/{id}/content', 'PlaylistController@delPlaylistContent')->name('remove_playlist_details');

  // Route::post('/{uuid}/playlist', 'PlaylistController@setPlaylistContent')->name('set_playlist_screen');
});
});



