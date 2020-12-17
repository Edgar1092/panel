<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect()->to("https://www.adhook.es/"); 
    // Route::get('home', 'HomeController@index')->name('home');
});

Route::prefix('profile')->group(function () {
    Route::get('/', 'UserController@index')->name('profile');
    Route::post('/', 'UserController@update');
    
    Route::match(['get', 'post'], 'password', 'UserController@password')->name('password');
});

Route::prefix('screens')->group(function () {
    Route::get('/', 'UserController@getScreens')->name('screens');
    Route::get('/{uuid}', 'UserController@getScreens')->name('screens_admin');
    Route::put('/', 'UserController@uptScreens');
    Route::post('/', 'UserController@addScreens');
    Route::delete('/', 'UserController@delScreens');
});
Route::prefix('users')->group(function () {
    Route::get('/', 'UserController@getUsers')->name('users');
    Route::get('/promotores', 'UserController@getUsersPromotores')->name('promotores');
    Route::post('update', 'UserController@edit')->name('users.update');
    Route::post('/', 'UserController@store');
    Route::delete('/', 'UserController@delete');
    Route::get('active/{uuid}', 'UserController@active')->name('users.active');
    Route::get('desactive/{uuid}', 'UserController@desactive')->name('users.desactive');
});
Route::prefix('content')->group(function () {
    //Route::get('/', 'ContentController@selectScreen')->name('content');
    Route::get('/', 'ContentController@userContent')->name('content');
    Route::post('/', 'ContentController@uploadContent');
    Route::put('/', 'ContentController@playlistContent');
    Route::delete('/', 'ContentController@delContent');
    Route::get('/{uuid}', 'ContentController@viewContent')->name('screen_content');
    Route::post('/{uuid}', 'ContentController@uploadContent');
    Route::put('/{uuid}', 'ContentController@playlistContent');
    Route::get('/admin/{uuid}', 'ContentController@userContent')->name('content_admin');
});

Route::prefix('playlist')->group(function () {
    Route::get('/', 'PlaylistController@getPlaylists')->name('playlist');
    Route::get('/admin/{uuid}', 'PlaylistController@getPlaylists')->name('playlist_admin');
    Route::post('/', 'PlaylistController@addPlaylist');
    Route::delete('/', 'PlaylistController@delPlaylist');
    
    Route::get('/{id}', 'PlaylistController@viewPlaylist')->name('playlist_details');
    Route::get('/admin/{id}/{uuid}', 'PlaylistController@viewPlaylist')->name('playlist_details_admin');
    Route::post('/bloquear/admin', 'PlaylistController@lockShcedule')->name('block_shcedule');
    Route::get('/desbloquear/admin/{id}/{uuid}/{idScreen}', 'PlaylistController@unlockShcedule')->name('unblock_shcedule');
    Route::put('/{id}', 'PlaylistController@updatePlaylist');

    Route::post('/{id}/content', 'PlaylistController@delPlaylistContent')->name('remove_playlist_details');

    Route::post('/{uuid}/playlist', 'PlaylistController@setPlaylistContent')->name('set_playlist_screen');
});

Route::view('/apps', 'working')->name('apps');
Route::view('/design', 'working')->name('design');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
