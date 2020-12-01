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







Route::group([
    'prefix' => 'user'
  ], function() {
        Route::post('login', 'api\v1\loginController@login');
        Route::post('register', 'api\v1\loginController@register');
        Route::group([
            'middleware' => 'auth:api'
          ], function() {
            Route::get('', 'api\v1\loginController@user');
            Route::put('save', 'api\v1\loginController@update');
            Route::post('update', 'api\v1\loginController@password');
            Route::post('avatar/save', 'api\v1\loginController@updatePhoto');
          });

  });



  Route::group([
    'prefix' => 'playlist'
  ], function() {
    Route::group([
        'middleware' => 'auth:api'
      ], function() {

    Route::get('{id}/content', 'api\v1\playlist\playlistController@viewPlaylist');
    Route::get('all', 'api\v1\playlist\playlistController@getPlaylists');
    Route::put('{id}/update', 'api\v1\playlist\playlistController@updatePlaylist');
    Route::post('add', 'api\v1\playlist\playlistController@addPlaylist');
    Route::delete('{id}/delete', 'api\v1\playlist\playlistController@delPlaylist');
    Route::post('content/add', 'api\v1\playlist\playlistController@setPlaylistContent');
    Route::delete('{id}/{cid}/delete', 'api\v1\playlist\playlistController@delPlaylistContent');
  });
});

Route::group([
    'prefix' => 'screen'
  ], function() {
    Route::group([
        'middleware' => 'auth:api'
      ], function() {
    Route::get('all', 'api\v1\playlist\playlistController@screens');
    Route::post('add', 'api\v1\playlist\playlistController@addScreens');
    Route::put('update', 'api\v1\playlist\playlistController@updateScreen');
     Route::post('schedule', 'api\v1\playlist\playlistController@addSchedule');
    Route::post('schedule/partial', 'api\v1\playlist\playlistController@addPartialSchedule');




  });
});



