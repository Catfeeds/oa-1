<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/7/30
 * Time: 10:50
 * 考勤模块路由
 */

Route::group([
    'middleware' => 'auth'
], function () {
    # 考勤系统路由模块
    Route::group(['namespace' => 'Attendance', 'prefix' => 'attendance'], function () {
        # 首页
        Route::get('index', [
            'uses' => 'IndexController@index',
        ])->name('attIndex');
        # 我的假期明细
        Route::get('leave', [
            'middleware' => ['permission:attendance-all|leave-all|leave'],
            'uses' => 'LeaveController@index'])->name('leave.info');
        Route::get('leave/create', [
            'middleware' => ['permission:attendance-all|leave-all|leave.create'],
            'uses' => 'LeaveController@create'])->name('leave.create');
        Route::post('leave/create', [
            'middleware' => ['permission:attendance-all|leave-all|leave.create'],
            'uses' => 'LeaveController@store']);
        Route::get('leave/edit', [
            'middleware' => ['permission:attendance-all|leave-all|leave-edit'],
            'uses' => 'LeaveController@edit'])->name('leave.edit');
        Route::post('leave/edit', [
            'middleware' => ['permission:attendance-all|leave-all|leave.edit'],
            'uses' => 'LeaveController@update']);

    });
});
