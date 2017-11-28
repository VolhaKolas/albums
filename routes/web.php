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


Auth::routes();

Route::get('/', 'HomeController@index')->name('home');



Route::group(['middleware' => ['auth']], function($router) {
    $router->get('/myAlbums', 'MyAlbumsController@index')->name('myAlbums');
    $router->get('/newAlbum', 'NewAlbumController@showNewAlbumForm')->name('newAlbum');
    $router->post('/newAlbum', 'NewAlbumController@createNewAlbum')->name('newAlbum');
    $router->post('/tracksCount', 'TracksCountController@count')->name('trackCount'); //ajax
    $router->get('/editAlbum/{album_id}', ['uses' => 'EditAlbumController@showEditForm']);
    $router->post('/editAlbum/{album_id}', ['uses' => 'EditAlbumController@editAlbum', 'as' => 'edit.album']);
    $router->post('/editAlbum/{album_id}/delete', ['uses' => 'EditAlbumController@delete', 'as' => 'delete.album']);
});