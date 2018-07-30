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
Route::get('/new-captcha', 'Auth\LoginController@captcha')->name('captcha');
Route::post('/sms', 'Auth\LoginController@validateSMS')->name('validateSMS');
Route::get('/we-chat-login', 'Auth\LoginController@weChatLogin')->name('weChatLogin');

Route::group(['middleware' => 'auth'], function () {
    // 首页
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home', 'HomeController@index')->name('home');

    //功能首页
    Route::get('index', [
        'uses' => 'AdminController@index',
    ])->name('index');
});
