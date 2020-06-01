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
Route::get('/', 'Upload\IndexController@getIndex')->name('home');
Route::get('/getListFile', 'Upload\IndexController@fileListAjax');
Route::resource('/download', 'Upload\DownloadController')->only(['store', 'destroy']);
Route::get('/filedownload/{name?}', 'Upload\FileDownloadController@fileDownload')->where('name', '.*');
Route::delete('/filedelete/{id}', 'Upload\FileDownloadController@fileDelete');
Route::post('/fileadd', 'Upload\FileDownloadController@fileadd');
