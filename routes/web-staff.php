<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/9/12
 * Time: 14:39
 * 员工管理路由管理
 */
//该路由不受登录限制，主要面对入职人员填写资料
Route::get('/entry/fill/{token}/{sign}', 'StaffManage\EntryController@fillInfo')->name('entry.send');
Route::post('/entry/fill/{token}/{sign}', 'StaffManage\EntryController@fill');
Route::get('/entry/fill/save', 'StaffManage\EntryController@save')->name('entry.save');

Route::group([
    'middleware' => 'auth'
], function () {
    # 员工管理路由模块
    Route::group(['namespace' => 'StaffManage', 'prefix' => 'staff'], function () {

        // 我的账号
        Route::get('profile', 'ProfileController@index')->name('profile');
        Route::get('profile/edit', 'ProfileController@edit')->name('profile.edit');
        Route::get('profile/confirm-edit', 'ProfileController@confirmEdit')->name('profile.confirmEdit');
        Route::post('profile/confirm-edit', 'ProfileController@confirmUpdate');
        Route::post('profile/edit', 'ProfileController@update');

        Route::get('profile/reset-password', [
            'middleware' => ['permission:profile.password'],
            'uses' => 'ProfileController@resetPassword'])->name('profile.reset-password');

        Route::post('profile/reset-password', 'ProfileController@resetPasswordUpdate');
        Route::get('profile/mail', 'ProfileController@mail')->name('profile.mail');


        #员工首页
        Route::get('index', [
            'middleware' => ['permission:manage.index'],
            'uses' => 'StaffController@staffManageIndex'])->name('manage.index');

        # 员工列表
        Route::get('list', [
            'middleware' => ['permission:staff'],
            'uses' => 'StaffController@index'])->name('staff.list');
        Route::get('edit/{id}', [
            'middleware' => ['permission:staff.edit'],
            'uses' => 'StaffController@edit'])->name('staff.edit');
        Route::post('edit/{id}', [
            'middleware' => ['permission:staff.edit'],
            'uses' => 'StaffController@update']);
        Route::get('export', [
            'middleware' => ['permission:staff.export'],
            'uses' => 'StaffController@export'])->name('staff.export');
        Route::get('exportAll', [
            'middleware' => ['permission:staff.export'],
            'uses' => 'StaffController@exportAll'])->name('staff.exportAll');
        Route::get('edit/info/{id}', [
            'middleware' => ['permission:staff.info'],
            'uses' => 'StaffController@info'])->name('staff.info');

        # 员工入职
        Route::get('entry', [
            'middleware' => ['permission:entry'],
            'uses' => 'EntryController@index'])->name('entry.list');
        Route::get('entry/create', [
            'middleware' => ['permission:entry.create'],
            'uses' => 'EntryController@create'])->name('entry.create');
        Route::post('entry/create', [
            'middleware' => ['permission:entry.create'],
            'uses' => 'EntryController@store']);
        Route::get('entry/edit/{id}', [
            'middleware' => ['permission:entry.edit'],
            'uses' => 'EntryController@edit'])->name('entry.edit');
        Route::post('entry/edit/{id}', [
            'middleware' => ['permission:entry.edit'],
            'uses' => 'EntryController@update']);

        Route::get('entry/edit-info/{id}', [
            'middleware' => ['permission:entry.edit'],
            'uses' => 'EntryController@editInfo'])->name('entry.editInfo');
        Route::post('entry/edit-info/{id}', [
            'middleware' => ['permission:entry.edit'],
            'uses' => 'EntryController@updateInfo']);

        Route::get('entry/create-send-info/{id}', [
            'middleware' => ['permission:entry.edit|entry.create|entry.sendMail'],
            'uses' => 'EntryController@createSendInfo'])->name('entry.createSendInfo');

        Route::get('entry/show-info/{id}', [
            'middleware' => ['permission:entry.edit|entry.create|entry.showInfo'],
            'uses' => 'EntryController@showInfo'])->name('entry.showInfo');

        Route::get('entry/del/{id}', [
            'middleware' => ['permission:entry.del'],
            'uses' => 'EntryController@del'])->name('entry.del');

        Route::get('entry/pass/{id}', [
            'middleware' => ['permission:entry.review'],
            'uses' => 'EntryController@pass'])->name('entry.pass');
        Route::get('entry/refuse/{id}', [
            'middleware' => ['permission:entry.review'],
            'uses' => 'EntryController@refuse'])->name('entry.refuse');

    });
});