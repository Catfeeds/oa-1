<?php
// 管理相关
Route::group([
    'middleware' => 'auth',
    'namespace' => 'Admin',
    'prefix' => 'admin',
], function () {
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

    // 员工管理
    Route::get('user', [
        'middleware' => ['permission:user-all|user'],
        'uses' => 'UserController@index'])->name('user');
    Route::get('user/create', [
        'middleware' => ['permission:user-all|user.create'],
        'uses' => 'UserController@create'])->name('user.create');
    Route::post('user/create', [
        'middleware' => ['permission:user-all|user.create'],
        'uses' => 'UserController@store']);
    Route::get('user/edit/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@edit'])->name('user.edit');
    Route::post('user/edit/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@update']);
    Route::get('user/is-mobile/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@isMobile'])->name('user.isMobile');
    Route::get('user/send-email/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@sendEmail'])->name('user.sendEmail');

    Route::get('user/ext/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@editExt'])->name('user.editExt');
    Route::post('user/ext/{id}', [
        'middleware' => ['permission:user-all|user.edit'],
        'uses' => 'UserController@updateExt']);

    // 职务管理
    Route::get('role', [
        'middleware' => ['permission:role-all|role'],
        'uses' => 'RoleController@index'])->name('role');
    Route::get('role/create', [
        'middleware' => ['permission:role-all|role.create'],
        'uses' => 'RoleController@create'])->name('role.create');
    Route::post('role/create', [
        'middleware' => ['permission:role-all|role.create'],
        'uses' => 'RoleController@store']);
    Route::get('role/edit/{id}', [
        'middleware' => ['permission:role-all|role.edit'],
        'uses' => 'RoleController@edit'])->name('role.edit');
    Route::post('role/edit/{id}', [
        'middleware' => ['permission:role-all|role.edit'],
        'uses' => 'RoleController@update']);

    // 角色权限指派
    Route::group([
        'middleware' => ['permission:role-all|role.appoint'],
    ], function () {
        Route::get('role/appoint/{id}', [
            'uses' => 'RoleController@appoint',
        ])->name('role.appoint');
        Route::post('role/appoint/{id}', [
            'uses' => 'RoleController@appointUpdate',
        ]);
    });

    //部门管理
    Route::get('sys/dept', [
        'middleware' => ['permission:attendance-all|dept-all|dept'],
        'uses' => 'Sys\DeptController@index'])->name('dept');
    Route::get('sys/dept/create', [
        'middleware' => ['permission:attendance-all|dept-all|dept.create'],
        'uses' => 'Sys\DeptController@create'])->name('dept.create');
    Route::post('sys/dept/create', [
        'middleware' => ['permission:attendance-all|dept-all|dept.create'],
        'uses' => 'Sys\DeptController@store']);
    Route::get('sys/dept/edit/{id}', [
        'middleware' => ['permission:attendance-all|dept-all|dept.edit'],
        'uses' => 'Sys\DeptController@edit'])->name('dept.edit');
    Route::post('sys/dept/edit/{id}', [
        'middleware' => ['permission:attendance-all|dept-all|dept.edit'],
        'uses' => 'Sys\DeptController@update']);

    //岗位管理
    Route::get('sys/job', [
        'middleware' => ['permission:attendance-all|job-all|job'],
        'uses' => 'Sys\JobController@index'])->name('job');
    Route::get('sys/job/create', [
        'middleware' => ['permission:attendance-all|job-all|job.create'],
        'uses' => 'Sys\JobController@create'])->name('job.create');
    Route::post('sys/job/create', [
        'middleware' => ['permission:attendance-all|job-all|job.create'],
        'uses' => 'Sys\JobController@store']);
    Route::get('sys/job/edit/{id}', [
        'middleware' => ['permission:attendance-all|job-all|job.edit'],
        'uses' => 'Sys\JobController@edit'])->name('job.edit');
    Route::post('sys/job/edit/{id}', [
        'middleware' => ['permission:attendance-all|job-all|job.edit'],
        'uses' => 'Sys\JobController@update']);

    //学校管理
    Route::get('sys/school', [
        'middleware' => ['permission:attendance-all|school-all|school'],
        'uses' => 'Sys\SchoolController@index'])->name('school');
    Route::get('sys/school/create', [
        'middleware' => ['permission:attendance-all|school-all|school.create'],
        'uses' => 'Sys\SchoolController@create'])->name('school.create');
    Route::post('sys/school/create', [
        'middleware' => ['permission:attendance-all|school-all|school.create'],
        'uses' => 'Sys\SchoolController@store']);
    Route::get('sys/school/edit/{id}', [
        'middleware' => ['permission:attendance-all|school-all|school.edit'],
        'uses' => 'Sys\SchoolController@edit'])->name('school.edit');
    Route::post('sys/school/edit/{id}', [
        'middleware' => ['permission:attendance-all|school-all|school.edit'],
        'uses' => 'Sys\SchoolController@update']);



});