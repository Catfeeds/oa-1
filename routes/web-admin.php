<?php
// 管理相关
Route::group([
    'middleware' => 'auth',
    'namespace' => 'Admin',
    'prefix' => 'admin',
], function () {

    // 帐号管理
    Route::get('user', [
        'middleware' => ['permission:user'],
        'uses' => 'UserController@index'])->name('user');
    Route::get('user/create', [
        'middleware' => ['permission:user.edit'],
        'uses' => 'UserController@create'])->name('user.create');
    Route::post('user/create', [
        'middleware' => ['permission:user.edit'],
        'uses' => 'UserController@store']);
    Route::get('user/edit/{id}', [
        'middleware' => ['permission:user.edit'],
        'uses' => 'UserController@edit'])->name('user.edit');
    Route::post('user/edit/{id}', [
        'middleware' => ['permission:user.edit'],
        'uses' => 'UserController@update']);
    Route::get('user/is-mobile/{id}', [
        'middleware' => ['permission:user.edit'],
        'uses' => 'UserController@isMobile'])->name('user.isMobile');
    Route::get('user/send-email/{id}', [
        'middleware' => ['permission:user.edit'],
        'uses' => 'UserController@sendEmail'])->name('user.sendEmail');

    Route::get('user/get-info', [
        'middleware' => ['permission:user'],
        'uses' => 'UserController@getInfoByCalendar'])->name('user.getInfo');

    // 权限管理
    Route::get('role', [
        'middleware' => ['permission:role'],
        'uses' => 'RoleController@index'])->name('role');
    Route::get('role/create', [
        'middleware' => ['permission:role.create'],
        'uses' => 'RoleController@create'])->name('role.create');
    Route::post('role/create', [
        'middleware' => ['permission:role.create'],
        'uses' => 'RoleController@store']);
    Route::get('role/edit/{id}', [
        'middleware' => ['permission:role.edit'],
        'uses' => 'RoleController@edit'])->name('role.edit');
    Route::post('role/edit/{id}', [
        'middleware' => ['permission:role.edit'],
        'uses' => 'RoleController@update']);

    // 角色权限指派
    Route::group([
        'middleware' => ['permission:role.appoint'],
    ], function () {
        Route::get('role/appoint/{id}', [
            'uses' => 'RoleController@appoint',
        ])->name('role.appoint');
        Route::get('role/get-appoint/{id}', [
            'uses' => 'RoleController@getAppoint',
        ])->name('role.getAppoint');
        Route::post('role/appoint/{id}', [
            'uses' => 'RoleController@appointUpdate',
        ]);
        Route::post('role/update-appoint/{id}', [
            'uses' => 'RoleController@appointUpdate',
        ])->name('role.appointUpdate');
    });

});