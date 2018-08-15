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
    Route::get('reconciliation-audit/edit/{id}/{source}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.edit'],
        'uses' => 'ReconciliationAuditController@edit'])->name('reconciliationAudit.edit');
    Route::post('reconciliation-audit/edit/{id}/{source}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.edit'],
        'uses' => 'ReconciliationAuditController@update']);
    Route::get('reconciliation-audit/review/{status}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.review'],
        'uses' => 'ReconciliationAuditController@review'])->name('reconciliationAudit.review');
    Route::get('reconciliation-audit/download', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationAudit|reconciliation-reconciliationAudit.download'],
        'uses' => 'ReconciliationAuditController@download'])->name('reconciliationAudit.download');

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
    Route::get('reconciliation-product', [
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
        'uses' => 'ReconciliationProductController@update']);

    //差异类管理
    Route::get('reconciliation-difference-type', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType'],
        'uses' => 'ReconciliationDifferenceTypeController@index'])->name('reconciliationDifferenceType');
    Route::get('reconciliation-difference-type/create/{pid}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType|reconciliation-reconciliationDifferenceType.create'],
        'uses' => 'ReconciliationDifferenceTypeController@create'])->name('reconciliationDifferenceType.create');
    Route::post('reconciliation-difference-type/create/{pid}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType|reconciliation-reconciliationDifferenceType.create'],
        'uses' => 'ReconciliationDifferenceTypeController@store']);
    Route::get('reconciliation-difference-type/edit/{id}/{pid}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType|reconciliation-reconciliationDifferenceType.edit'],
        'uses' => 'ReconciliationDifferenceTypeController@edit'])->name('reconciliationDifferenceType.edit');
    Route::post('reconciliation-difference-type/edit/{id}/{pid}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationDifferenceType|reconciliation-reconciliationDifferenceType.edit'],
        'uses' => 'ReconciliationDifferenceTypeController@update']);

    //分成比例管理
    Route::get('reconciliation-proportion', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProportion'],
        'uses' => 'ReconciliationProportionController@index'])->name('reconciliationProportion');
    Route::get('reconciliation-proportion/edit/{id}/{pid}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProportion|reconciliation-reconciliationProportion.edit'],
        'uses' => 'ReconciliationProportionController@edit'])->name('reconciliationProportion.edit');
    Route::post('reconciliation-proportion/edit/{id}/{pid}', [
        'middleware' => ['permission:crm-all|reconciliation-all|reconciliation-reconciliationProportion|reconciliation-reconciliationProportion.edit'],
        'uses' => 'ReconciliationProportionController@update']);

});