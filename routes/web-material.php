<?php
Route::group([
    'middleware' => 'auth',
    'namespace' => 'Material',
    'prefix' => 'material',
], function (){
    Route::get('apply/index', [
        'middleware' => ['permission:material.apply.index'],
        'uses' => 'MaterialController@applyIndex'])->name('material.apply.index');
    Route::get('apply/index/all', [
        'middleware' => ['permission:material.apply.index'],
        'uses' => 'MaterialController@showAllApply'])->name('material.apply.index-all');

    Route::get('apply/create', [
        'middleware' => ['permission:material.apply.create'],
        'uses' => 'MaterialController@applyCreate'])->name('material.apply.create');
    Route::post('apply/create', [
        'middleware' => ['permission:material.apply.create'],
        'uses' => 'MaterialController@applyStore']);

    Route::get('apply/info/id_type/{id}/{type}', [
        'middleware' => ['permission:material.apply.info'],
        'uses' => 'MaterialController@optInfo'])->name('material.apply.info');
    Route::get('apply/info/redraw/{id}', [
        'middleware' => ['permission:material.apply.redraw'],
        'uses' => 'MaterialController@redrawApply'])->name('material.apply.redraw');

    Route::get('approve/info/id_type/{id}/{type}', [
        'middleware' => ['permission:material.approve.info'],
        'uses' => 'MaterialController@optInfo'])->name('material.approve.info');

    Route::get('approve/index/state/{state}', [
        'middleware' => ['permission:material.approve.index'],
        'uses' => 'MaterialController@approveIndex'])->name('material.approve.index');

    Route::get('approve/info/optStatus/{id}', [
        'middleware' => ['permission:material.approve.info'],
        'uses' => 'MaterialController@reviewOptStatus'])->name('material.approve.optStatus');

});