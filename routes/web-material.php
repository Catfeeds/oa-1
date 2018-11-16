<?php
Route::group([
    'middleware' => 'auth',
    'namespace' => 'Material',
    'prefix' => 'material',
], function (){
    Route::get('apply/index', [
        /*'middleware' => 'material.apply.index'*/
        'uses' => 'MaterialController@applyIndex'])->name('material.apply.index');

    Route::get('apply/create', [
        /*'middleware' => 'material.apply.create'*/
        'uses' => 'MaterialController@applyCreate'])->name('material.apply.create');
    Route::post('apply/create', [
        /*'middleware' => 'material.apply.create'*/
        'uses' => 'MaterialController@applyStore']);

    Route::get('apply/info', [
        /*'middleware' => 'material.apply.info'*/
        'uses' => 'MaterialController@optInfo'])->name('material.apply.info');
    Route::get('apply/info/redraw/{id}', [
        /*'middleware' => 'material.apply.cancel'*/
        'uses' => 'MaterialController@redrawApply'])->name('material.apply.redraw');

    Route::get('approve/info/{id}', [
        /*'middleware' => 'material.approve.info'*/
        'uses' => 'MaterialController@optInfo'])->name('material.approve.info');

    Route::get('approve/index', [
        /*'middleware' => 'material.approve.index'*/
        'uses' => 'MaterialController@approveIndex'])->name('material.approve.index');

    Route::get('approve/index/state/{state}', [
        /*'middleware' => 'material.approve.index'*/
        'uses' => 'MaterialController@selectByState'])->name('material.approve.state');

    Route::get('approve/info/optStatus/{id}', [
        /*'middleware' => 'material.approve.info'*/
        'uses' => 'MaterialController@reviewOptStatus'])->name('material.approve.optStatus');

    Route::get('approve/confirm_return', [
        /*'middleware' => 'material.approve.info'*/
        'uses' => 'MaterialController@confirmReturn'])->name('material.approve.return');
});