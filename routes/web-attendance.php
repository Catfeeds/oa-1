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
            'middleware' => ['permission:leave-all|leave'],
            'uses' => 'LeaveController@index'])->name('leave.info');
        Route::get('leave/create', [
            'middleware' => ['permission:leave-all|leave.create'],
            'uses' => 'LeaveController@create'])->name('leave.create');
        Route::post('leave/create', [
            'middleware' => ['permission:leave-all|leave.create'],
            'uses' => 'LeaveController@store']);
        Route::get('leave/edit', [
            'middleware' => ['permission:leave-all|leave.edit'],
            'uses' => 'LeaveController@edit'])->name('leave.edit');
        Route::post('leave/edit', [
            'middleware' => ['permission:leave-all|leave.edit'],
            'uses' => 'LeaveController@update']);
        Route::get('leave/optInfo/{id}', [
            'middleware' => ['permission:leave-all|leave.edit|leave.create'],
            'uses' => 'LeaveController@optInfo'])->name('leave.optInfo');
        #申请单管理
        Route::get('leave/review/', [
            'middleware' => ['permission:leave-all|leave.review'],
            'uses' => 'LeaveController@reviewIndex'])->name('leave.review.info');

        Route::get('leave/review/optInfo/{id}', [
            'middleware' => ['permission:leave-all|leave.review'],
            'uses' => 'LeaveController@optInfo'])->name('leave.review.optInfo');

        Route::get('leave/review/{id}', [
            'middleware' => ['permission:leave-all|leave.review.optStatus'],
            'uses' => 'LeaveController@reviewOptStatus'])->name('leave.review.optStatus');
        Route::get('leave/review-batch/{id}', [
            'middleware' => ['permission:leave-all|leave.review.optStatus'],
            'uses' => 'LeaveController@reviewBatchOptStatus'])->name('leave.review.batchOptStatus');

    });
});
