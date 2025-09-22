<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'DashboardController@index')->name('depan');
Route::get('/display/antrian', 'KunjunganController@DisplayAntrian')->name('display.antrian');

Route::get('/webapi', 'WebapiController@WebApi')->name('webapi');
Route::get('logout', '\App\Http\Controllers\PetugasController@logout');
Route::group(['middleware' => ['auth']], function () {

});
