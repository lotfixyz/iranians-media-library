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

/*
Route::get('/', function () {
    return view('welcome');
});
*/

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/', 'namespace' => 'Frontend'], function () {
    Route::get('', 'FrontendController@index')->name('frontend.index');
    Route::get('{domain}/{current_folder?}', 'FrontendController@domain')
        ->name('frontend.domain')
        //->where('current_folder', '[a-zA-Z0-9=]+');
        ->where('current_folder', '[a-zA-Z0-9-_)(/\.،\s\p{Arabic}]+');
});

Route::group(['prefix' => env('DOWNLOAD_FOLDER'), 'namespace' => 'Frontend'], function () {
    Route::get('/{file}')->name('frontend.' . env('DOWNLOAD_FOLDER'));
});
