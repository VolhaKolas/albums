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
    $router->get('/newAlbum', 'NewAlbumController@index')->name('newAlbum');
    $router->post('/newAlbum', 'NewAlbumController@post')->name('newAlbum');
    $router->post('/tracksCount', 'TracksCountController@post')->name('trackCount');
    $router->get('/editAlbum', 'EditAlbumController@index')->name('editAlbum');
    $router->post('/editAlbum', 'EditAlbumController@post')->name('editAlbum');
    $router->post('/deleteAlbum', 'DeleteAlbumController@post')->name('deleteAlbum');
});