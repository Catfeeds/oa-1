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
        # ajax获取工作日历
        Route::get('calIndex', [
            'uses' => 'IndexController@getCalendarByAjax',
        ])->name('calIndex');

        # 我的假期明细
        Route::get('leave', [
            'middleware' => ['permission:attendance-all|leave-all|leave'],
            'uses' => 'LeaveController@index'])->name('leave.info');
        Route::get('leave/create/{id}', [
            'middleware' => ['permission:attendance-all|leave-all|leave.create'],
            'uses' => 'LeaveController@create'])->name('leave.create');
        Route::post('leave/create/{id}', [
            'middleware' => ['permission:attendance-all|leave-all|leave.create'],
            'uses' => 'LeaveController@store']);
        Route::get('leave/edit', [
            'middleware' => ['permission:attendance-all|leave-all|leave.edit'],
            'uses' => 'LeaveController@edit'])->name('leave.edit');
        Route::post('leave/edit', [
            'middleware' => ['permission:attendance-all|leave-all|leave.edit'],
            'uses' => 'LeaveController@update']);
        Route::get('leave/optInfo/{id}/{type}', [
            'middleware' => ['permission:attendance-all|leave-all|leave.edit|leave.create'],
            'uses' => 'LeaveController@optInfo'])->name('leave.optInfo');

        #申请单管理
        Route::get('leave/review/', [
            'middleware' => ['permission:attendance-all|leave-all|leave.review'],
            'uses' => 'LeaveController@reviewIndex'])->name('leave.review.info');
        Route::get('leave/review/optInfo/{id}/{type}', [
            'middleware' => ['permission:attendance-all|leave-all|leave.review'],
            'uses' => 'LeaveController@optInfo'])->name('leave.review.optInfo');
        //批量操作
        Route::get('leave/review/{id}', [
            'middleware' => ['permission:attendance-all|leave-all|leave.review'],
            'uses' => 'LeaveController@reviewOptStatus'])->name('leave.review.optStatus');
        Route::get('leave/review-batch/{id}', [
            'middleware' => ['permission:attendance-all|leave-all|leave.review'],
            'uses' => 'LeaveController@reviewBatchOptStatus'])->name('leave.review.batchOptStatus');

        Route::get('daily-detail', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail'],
            'uses' => 'DailyDetailController@index'])->name('daily-detail.info');

        //用户点击考勤确认
        Route::get('daily-detail/confirm', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail'],
            'uses' => 'DailyDetailController@confirm'])->name('daily-detail.confirm');
        //考勤管理
        Route::get('daily-detail/review/', [
            'middleware' => ['permission:daily-detail.review'],
            'uses' => 'ReviewController@index'])->name('daily-detail.review.info');
        //发布用户考勤确认信息
        Route::get('daily-detail/review/send', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail.review'],
            'uses' => 'ReviewController@send'])->name('daily-detail.review.send');
        #明细
        Route::get('daily-detail/review/user-id/{id}', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail'],
            'uses' => 'ReviewController@reviewDetail'])->name('daily-detail.review.user');

        #考勤导入功能
        Route::get('daily-detail/review/import/info', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail.review'],
            'uses' => 'PunchRecordController@index'])->name('daily-detail.review.import.info');
        Route::get('daily-detail/review/import', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail.review'],
            'uses' => 'PunchRecordController@create'])->name('daily-detail.review.import');
        Route::post('daily-detail/review/import', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail.review'],
            'uses' => 'PunchRecordController@store']);
        Route::get('daily-detail/review/import/edit/{id}', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail.review'],
            'uses' => 'PunchRecordController@edit'])->name('daily-detail.review.import.edit');
        Route::post('daily-detail/review/import/edit/{id}', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail.review'],
            'uses' => 'PunchRecordController@update']);
        Route::get('daily-detail/review/import/generate/{id}', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail.review'],
            'uses' => 'PunchRecordController@generate'])->name('daily-detail.review.import.generate');
        Route::get('daily-detail/review/import/generate/log/{id}', [
            'middleware' => ['permission:attendance-all|daily-detail-all|daily-detail.review'],
            'uses' => 'PunchRecordController@log'])->name('daily-detail.review.import.generate.log');
    });
});
