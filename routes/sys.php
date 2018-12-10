<?php
/**
 * Created by PhpStorm.
 * User: weiming Email: 329403630@qq.com
 * Date: 2018/12/10
 * Time: 11:32
 * 系统配置路由
 */

Route::group([
    'middleware' => 'auth'
], function () {
    # 系统配管理路由模块
    Route::group(['namespace' => 'Sys', 'prefix' => 'sys'], function () {
        //部门管理
        Route::get('dept', [
            'middleware' => ['permission:dept'],
            'uses' => 'DeptController@index'])->name('dept');
        Route::get('dept/create', [
            'middleware' => ['permission:dept.create'],
            'uses' => 'DeptController@create'])->name('dept.create');
        Route::post('dept/create', [
            'middleware' => ['permission:dept.create'],
            'uses' => 'DeptController@store']);
        Route::get('dept/edit/{id}', [
            'middleware' => ['permission:dept.edit'],
            'uses' => 'DeptController@edit'])->name('dept.edit');
        Route::post('dept/edit/{id}', [
            'middleware' => ['permission:dept.edit'],
            'uses' => 'DeptController@update']);
        Route::get('dept/get-child/', [
            'middleware' => ['permission:dept.edit|dept.create'],
            'uses' => 'DeptController@getChild'])->name('dept.getChild');
        Route::get('dept/del/{id}', [
            'middleware' => ['permission:dept.del'],
            'uses' => 'DeptController@del'])->name('dept.del');

        //岗位管理
        Route::get('job', [
            'middleware' => ['permission:job'],
            'uses' => 'JobController@index'])->name('job');
        Route::get('job/create', [
            'middleware' => ['permission:job.create'],
            'uses' => 'JobController@create'])->name('job.create');
        Route::post('job/create', [
            'middleware' => ['permission:job.create'],
            'uses' => 'JobController@store']);
        Route::get('job/edit/{id}', [
            'middleware' => ['permission:job.edit'],
            'uses' => 'JobController@edit'])->name('job.edit');
        Route::post('job/edit/{id}', [
            'middleware' => ['permission:job.edit'],
            'uses' => 'JobController@update']);
        Route::get('job/del/{id}', [
            'middleware' => ['permission:job.del'],
            'uses' => 'JobController@del'])->name('job.del');

        //学校管理
        Route::get('school', [
            'middleware' => ['permission:school'],
            'uses' => 'SchoolController@index'])->name('school');
        Route::get('school/create', [
            'middleware' => ['permission:school.create'],
            'uses' => 'SchoolController@create'])->name('school.create');
        Route::post('school/create', [
            'middleware' => ['permission:school.create'],
            'uses' => 'SchoolController@store']);
        Route::get('school/edit/{id}', [
            'middleware' => ['permission:school.edit'],
            'uses' => 'SchoolController@edit'])->name('school.edit');
        Route::post('school/edit/{id}', [
            'middleware' => ['permission:school.edit'],
            'uses' => 'SchoolController@update']);
        Route::get('school/del/{id}', [
            'middleware' => ['permission:school.del'],
            'uses' => 'SchoolController@del'])->name('school.del');

        //假期配置管理
        Route::get('holiday-config', [
            'middleware' => ['permission:holiday-config'],
            'uses' => 'HolidayConfigController@index'])->name('holiday-config');
        Route::get('holiday-config/create', [
            'middleware' => ['permission:holiday-config.create'],
            'uses' => 'HolidayConfigController@create'])->name('holiday-config.create');
        Route::post('holiday-config/create', [
            'middleware' => ['permission:holiday-config.create'],
            'uses' => 'HolidayConfigController@store']);
        Route::get('holiday-config/edit/{id}', [
            'middleware' => ['permission:holiday-config.edit'],
            'uses' => 'HolidayConfigController@edit'])->name('holiday-config.edit');
        Route::post('holiday-config/edit/{id}', [
            'middleware' => ['permission:holiday-config.edit'],
            'uses' => 'HolidayConfigController@update']);

        //审核流程配置管理-启用
        Route::get('review-step-flow', [
            'middleware' => ['permission:approval-step'],
            'uses' => 'ReviewStepFlowController@index'])->name('review-step-flow');
        Route::get('review-step-flow/create', [
            'middleware' => ['permission:approval-step.create'],
            'uses' => 'ReviewStepFlowController@create'])->name('review-step-flow.create');
        Route::post('review-step-flow/create', [
            'middleware' => ['permission:approval-step.create'],
            'uses' => 'ReviewStepFlowController@store']);
        Route::get('review-step-flow/edit/{id}', [
            'middleware' => ['permission:approval-step.edit'],
            'uses' => 'ReviewStepFlowController@edit'])->name('review-step-flow.edit');
        Route::post('review-step-flow/edit/{id}', [
            'middleware' => ['permission:approval-step.edit'],
            'uses' => 'ReviewStepFlowController@update']);
        Route::get('review-step-flow/get-holiday', [
            'middleware' => ['permission:approval-step.edit'],
            'uses' => 'ReviewStepFlowController@getHoliday'])->name('review-step-flow.getHoliday');


        //上下班时间配置管理
        Route::get('punch-rules', [
            'middleware' => ['permission:punch-rules'],
            'uses' => 'PunchRulesController@index'])->name('punch-rules');
        Route::get('punch-rules/create', [
            'middleware' => ['permission:punch-rules.create'],
            'uses' => 'PunchRulesController@create'])->name('punch-rules.create');
        Route::post('punch-rules/create', [
            'middleware' => ['permission:punch-rules.create'],
            'uses' => 'PunchRulesController@store']);
        Route::get('punch-rules/edit/{id}', [
            'middleware' => ['permission:punch-rules.edit'],
            'uses' => 'PunchRulesController@edit'])->name('punch-rules.edit');
        Route::post('punch-rules/edit/{id}', [
            'middleware' => ['permission:punch-rules.edit'],
            'uses' => 'PunchRulesController@update']);

        //日历表配置
        Route::get('calendar', [
            'middleware' => ['permission:calendar'],
            'uses' => 'CalendarController@index'])->name('calendar');
        Route::get('calendar/create', [
            'middleware' => ['permission:calendar.create'],
            'uses' => 'CalendarController@create'])->name('calendar.create');
        Route::post('calendar/create', [
            'middleware' => ['permission:calendar.create'],
            'uses' => 'CalendarController@store']);
        Route::get('calendar/edit/{id}', [
            'middleware' => ['permission:calendar.edit'],
            'uses' => 'CalendarController@edit'])->name('calendar.edit');
        Route::post('calendar/edit/{id}', [
            'middleware' => ['permission:calendar.edit'],
            'uses' => 'CalendarController@update']);

        Route::post('calendar/store-month', [
            'middleware' => ['permission:punch-rules-all|calendar'],
            'uses' => 'CalendarController@storeAllMonth'])->name('calendar.storeMonth');

        Route::get('calendar/show-list', [
            'middleware' => ['permission:calendar'],
            'uses' => 'CalendarController@list'])->name('calendar.list');

        # 公司配置
        Route::get('firm', [
            'middleware' => ['permission:firm'],
            'uses' => 'FirmController@index'])->name('firm');
        Route::get('firm/create', [
            'middleware' => ['permission:firm.create'],
            'uses' => 'FirmController@create'])->name('firm.create');
        Route::post('firm/create', [
            'middleware' => ['permission:firm.create'],
            'uses' => 'FirmController@store']);
        Route::get('firm/edit/{id}', [
            'middleware' => ['permission:firm.edit'],
            'uses' => 'FirmController@edit'])->name('firm.edit');
        Route::post('firm/edit/{id}', [
            'middleware' => ['permission:firm.edit'],
            'uses' => 'FirmController@update']);
        Route::get('firm/del/{id}', [
            'middleware' => ['permission:firm.del'],
            'uses' => 'FirmController@del'])->name('firm.del');

        # 民族配置
        Route::get('ethnic', [
            'middleware' => ['permission:ethnic'],
            'uses' => 'EthnicController@index'])->name('ethnic');
        Route::get('ethnic/create', [
            'middleware' => ['permission:ethnic.create'],
            'uses' => 'EthnicController@create'])->name('ethnic.create');
        Route::post('ethnic/create', [
            'middleware' => ['permission:ethnic.create'],
            'uses' => 'EthnicController@store']);
        Route::get('ethnic/edit/{id}', [
            'middleware' => ['permission:ethnic.edit'],
            'uses' => 'EthnicController@edit'])->name('ethnic.edit');
        Route::post('ethnic/edit/{id}', [
            'middleware' => ['permission:ethnic.edit'],
            'uses' => 'EthnicController@update']);
        Route::get('ethnic/del/{id}', [
            'middleware' => ['permission:ethnic.del'],
            'uses' => 'EthnicController@del'])->name('ethnic.del');


        #公告栏配置
        Route::get('bulletin', [
            'middleware' => ['permission:bulletin.index'],
            'uses' => 'BulletinController@index'])->name('bulletin.index');
        Route::get('bulletin/create', [
            'middleware' => ['permission:bulletin.create'],
            'uses' => 'BulletinController@create'])->name('bulletin.create');
        Route::post('bulletin/create', [
            'middleware' => ['permission:bulletin.create'],
            'uses' => 'BulletinController@store']);
        Route::get('bulletin/edit/{id}', [
            'middleware' => ['permission:bulletin.edit'],
            'uses' => 'BulletinController@edit'])->name('bulletin.edit');
        Route::post('bulletin/edit/{id}', [
            'middleware' => ['permission:bulletin.edit'],
            'uses' => 'BulletinController@update']);
        Route::get('bulletin/change-show', [
            'middleware' => ['permission:bulletin.changeShow'],
            'uses' => 'BulletinController@changeShow'])->name('bulletin.changeShow');

        Route::get('bulletin/show-bulletin/{id}', [
            'uses' => 'BulletinController@showBulletin'])->name('bulletin.show');

        #资质外借库存配置
        Route::get('inventory', [
            'middleware' => ['permission:material.inventory-list'],
            'uses' => 'InventoryController@inventoryList'])->name('inventory.list');

        Route::get('inventory/create', [
            'middleware' => ['permission:material.inventory-create'],
            'uses' => 'InventoryController@create'])->name('inventory.create');
        Route::post('inventory/create', [
            'middleware' => ['permission:material.inventory-create'],
            'uses' => 'InventoryController@store']);

        Route::get('inventory/edit/{id}', [
            'middleware' => ['permission:material.inventory-edit'],
            'uses' => 'InventoryController@edit'])->name('inventory.edit');
        Route::post('inventory/edit/{id}', [
            'middleware' => ['permission:material.inventory-edit'],
            'uses' => 'InventoryController@update']);

        Route::get('inventory/upload', [
            'middleware' => ['permission:material.inventory-upload'],
            'uses' => 'InventoryController@upload'])->name('inventory.upload');
        Route::post('inventory/upload', [
            'middleware' => ['permission:material.inventory-upload'],
            'uses' => 'InventoryController@excel']);

    });
});