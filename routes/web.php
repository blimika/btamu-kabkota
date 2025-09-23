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
Route::get('/login', 'PetugasController@showLoginForm')->name('login');
Route::post('/login', 'PetugasController@login')->name('login.proses');
Route::get('/logout', 'PetugasController@logout')->name('logout');

Route::group(['middleware' => 'ip.or.login'], function () {
    Route::get('/kunjungan/tambah', 'KunjunganController@tambah')->name('kunjungan.tambah');
    Route::post('/kunjungan/simpan', 'KunjunganController@simpan')->name('kunjungan.simpan');
    Route::get('/webapi', 'WebapiController@WebApi')->name('webapi');
});
Route::group(['middleware' => ['auth']], function () {

});
