<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('floorplan')->group(function() {
    Route::get('/', 'FloorplanController@index');
});

Route::group([
    'middleware' =>[ 'web', 'token_auth', 'user_menu'],
    'namespace' => 'Modules\Floorplan\Http\Controllers'
], function () {
    Route::prefix('tables/floorplan')->group(function() {
        Route::get('/{restoarea}', 'Main@edit')->name('floorplan.edit');
        Route::post('/save/{restoarea}', 'Main@saveFloorPlan')->name('floorplan.save');
    });
});