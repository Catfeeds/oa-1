<?php
// 对账相关
Route::group([
    'middleware' => 'auth',
    'namespace' => 'Crm',
    'prefix' => 'crm',
], function () {
    # 首页
    Route::get('index', [
        'uses' => 'IndexController@index',
    ])->name('CrmIndex');

    // 对账审核功能
    Route::get('reconciliation-audit', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit'],
        'uses' => 'ReconciliationAuditController@index'])->name('reconciliationAudit');
    Route::get('reconciliation-audit/data', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit'],
        'uses' => 'ReconciliationAuditController@data'])->name('reconciliationAudit.data');
    Route::get('reconciliation-audit/edit/{id}/{source}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.edit'],
        'uses' => 'ReconciliationAuditController@edit'])->name('reconciliationAudit.edit');
    Route::post('reconciliation-audit/edit/{id}/{source}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.edit'],
        'uses' => 'ReconciliationAuditController@update']);
    Route::get('reconciliation-audit/review/{status}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.review'],
        'uses' => 'ReconciliationAuditController@review'])->name('reconciliationAudit.review');
    Route::get('reconciliation-audit/invoice', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.invoice'],
        'uses' => 'ReconciliationAuditController@invoice'])->name('reconciliationAudit.invoice');
    Route::get('reconciliation-audit/payback', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.payback'],
        'uses' => 'ReconciliationAuditController@payback'])->name('reconciliationAudit.payback');
    Route::get('reconciliation-audit/revision', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.revision'],
        'uses' => 'ReconciliationAuditController@revision'])->name('reconciliationAudit.revision');

    //负责人
    Route::get('reconciliation-principal', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationPrincipal'],
        'uses' => 'ReconciliationPrincipalController@index'])->name('reconciliationPrincipal');
    Route::get('reconciliation-principal/data', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationPrincipal'],
        'uses' => 'ReconciliationPrincipalController@data'])->name('reconciliationPrincipal.data');
    Route::get('reconciliation-principal/edit', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationPrincipal|reconciliation-reconciliationPrincipal.edit'],
        'uses' => 'ReconciliationPrincipalController@edit'])->name('reconciliationPrincipal.edit');
    Route::post('reconciliation-principal/edit', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationPrincipal|reconciliation-reconciliationPrincipal.edit'],
        'uses' => 'ReconciliationPrincipalController@update']);

    //游戏列表
    /*Route::get('reconciliation-product', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProduct'],
        'uses' => 'ReconciliationProductController@index'])->name('reconciliationProduct');
    Route::get('reconciliation-product/create', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProduct|reconciliation-reconciliationProduct.create'],
        'uses' => 'ReconciliationProductController@create'])->name('reconciliationProduct.create');
    Route::post('reconciliation-product/create', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProduct|reconciliation-reconciliationProduct.create'],
        'uses' => 'ReconciliationProductController@store']);
    Route::get('reconciliation-product/edit/{id}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProduct|reconciliation-reconciliationProduct.edit'],
        'uses' => 'ReconciliationProductController@edit'])->name('reconciliationProduct.edit');
    Route::post('reconciliation-product/edit/{id}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProduct|reconciliation-reconciliationProduct.edit'],
        'uses' => 'ReconciliationProductController@update']);*/

    //差异类管理
    Route::get('reconciliation-difference-type', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType'],
        'uses' => 'ReconciliationDifferenceTypeController@index'])->name('reconciliationDifferenceType');
    Route::get('reconciliation-difference-type/create', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType|reconciliation-reconciliationDifferenceType.create'],
        'uses' => 'ReconciliationDifferenceTypeController@create'])->name('reconciliationDifferenceType.create');
    Route::post('reconciliation-difference-type/create', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType|reconciliation-reconciliationDifferenceType.create'],
        'uses' => 'ReconciliationDifferenceTypeController@store']);
    Route::get('reconciliation-difference-type/edit/{id}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType|reconciliation-reconciliationDifferenceType.edit'],
        'uses' => 'ReconciliationDifferenceTypeController@edit'])->name('reconciliationDifferenceType.edit');
    Route::post('reconciliation-difference-type/edit/{id}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType|reconciliation-reconciliationDifferenceType.edit'],
        'uses' => 'ReconciliationDifferenceTypeController@update']);

    //分成比例管理
    Route::get('reconciliation-proportion', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProportion'],
        'uses' => 'ReconciliationProportionController@index'])->name('reconciliationProportion');
    Route::get('reconciliation-proportion/data', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProportion'],
        'uses' => 'ReconciliationProportionController@data'])->name('reconciliationProportion.data');
    Route::get('reconciliation-proportion/edit/{id}/{pid}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProportion|reconciliation-reconciliationProportion.edit'],
        'uses' => 'ReconciliationProportionController@edit'])->name('reconciliationProportion.edit');
    Route::post('reconciliation-proportion/edit/{id}/{pid}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProportion|reconciliation-reconciliationProportion.edit'],
        'uses' => 'ReconciliationProportionController@update']);
    Route::get('reconciliation-proportion/batch', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProportion|reconciliation-reconciliationProportion.batch'],
        'uses' => 'ReconciliationProportionController@batch'])->name('reconciliationProportion.batch');
    Route::post('reconciliation-proportion/batch', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProportion|reconciliation-reconciliationProportion.batch'],
        'uses' => 'ReconciliationProportionController@add']);

    //货币汇率
    Route::get('reconciliation-exchange-rate', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationExchangeRate'],
        'uses' => 'ReconciliationExchangeRateController@index'])->name('reconciliationExchangeRate');
    Route::get('reconciliation-exchange-rate/create', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationExchangeRate|reconciliation-reconciliationExchangeRate.create'],
        'uses' => 'ReconciliationExchangeRateController@create'])->name('reconciliationExchangeRate.create');
    Route::post('reconciliation-exchange-rate/create', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationExchangeRate|reconciliation-reconciliationExchangeRate.create'],
        'uses' => 'ReconciliationExchangeRateController@store']);
    Route::get('reconciliation-exchange-rate/edit/{id}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationExchangeRate|reconciliation-reconciliationExchangeRate.edit'],
        'uses' => 'ReconciliationExchangeRateController@edit'])->name('reconciliationExchangeRate.edit');
    Route::post('reconciliation-exchange-rate/edit/{id}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationExchangeRate|reconciliation-reconciliationExchangeRate.edit'],
        'uses' => 'ReconciliationExchangeRateController@update']);
    Route::get('reconciliation-exchange-rate/conversion', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationExchangeRate|reconciliation-reconciliationExchangeRate.conversion'],
        'uses' => 'ReconciliationExchangeRateController@conversion'])->name('reconciliationExchangeRate.conversion');

    //对账汇总
    Route::get('reconciliation-pool', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationPool'],
        'uses' => 'ReconciliationPoolController@index'])->name('reconciliationPool');
    Route::get('reconciliation-pool/data', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationPool'],
        'uses' => 'ReconciliationPoolController@data'])->name('reconciliationPool.data');
    Route::get('reconciliation-pool/detail', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationPool|reconciliation-reconciliationPool.detail'],
        'uses' => 'ReconciliationPoolController@detail'])->name('reconciliationPool.detail');

    //对账进度跟踪表
    Route::get('reconciliation-schedule', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationSchedule'],
        'uses' => 'ReconciliationScheduleController@index'])->name('reconciliationSchedule');
    Route::get('reconciliation-schedule/data', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationSchedule'],
        'uses' => 'ReconciliationScheduleController@data'])->name('reconciliationSchedule.data');
    Route::get('reconciliation-schedule/detail', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationPool|reconciliation-reconciliationSchedule.detail'],
        'uses' => 'ReconciliationScheduleController@detail'])->name('reconciliationSchedule.detail');
});