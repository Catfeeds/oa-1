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

    // 我的账号
    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::get('/profile/edit', 'ProfileController@edit')->name('profile.edit');
    Route::post('/profile/edit', 'ProfileController@update');

    Route::get('/profile/reset-password', [
        'middleware' => ['permission:profile.password'],
        'uses' => 'ProfileController@resetPassword'])->name('profile.reset-password');

    Route::post('/profile/reset-password', 'ProfileController@resetPasswordUpdate');
    Route::get('/profile/mail', 'ProfileController@mail')->name('profile.mail');
    // 账号管理
    Route::get('/user', [
        'middleware' => ['permission:user-all|user'],
        'uses' => 'UserController@index'])->name('user');
    Route::get('/user/create', [
        'middleware' => ['permission:user-all|user.create'],
        'uses' => 'UserController@create'])->name('user.create');
    Route::post('/user/create', [
        'middleware' => ['permission:user-all|user.create'],
        'uses' => 'UserController@store']);
    Route::get('/user/edit/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@edit'])->name('user.edit');
    Route::post('/user/edit/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@update']);
    Route::get('/user/is-mobile/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@isMobile'])->name('user.isMobile');
    Route::get('/user/send-email/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@sendEmail'])->name('user.sendEmail');

    // 角色管理
    Route::get('/role', [
        'middleware' => ['permission:role-all|role'],
        'uses' => 'RoleController@index'])->name('role');
    Route::get('/role/create', [
        'middleware' => ['permission:role-all|role.create'],
        'uses' => 'RoleController@create'])->name('role.create');
    Route::post('/role/create', [
        'middleware' => ['permission:role-all|role.create'],
        'uses' => 'RoleController@store']);
    Route::get('/role/edit/{id}', [
        'middleware' => ['permission:role-all|role.edit'],
        'uses' => 'RoleController@edit'])->name('role.edit');
    Route::post('/role/edit/{id}', [
        'middleware' => ['permission:role-all|role.edit'],
        'uses' => 'RoleController@update']);
    // 角色权限指派
    Route::group([
        'middleware' => ['permission:role-all|role.appoint'],
    ], function () {
        Route::get('/role/appoint/{id}', [
            'uses' => 'RoleController@appoint',
        ])->name('role.appoint');
        Route::post('/role/appoint/{id}', [
            'uses' => 'RoleController@appointUpdate',
        ]);
    });
});
