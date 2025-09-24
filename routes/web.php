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
//feedback
Route::post('/feedbacksave', 'KunjunganController@FeedbackSave')->name('feedbacksave');
Route::get('/kunjungan/feedback/{uid}', 'KunjunganController@NewFeedback')->name('kunjungan.feedback');
Route::group(['middleware' => 'ip.or.login'], function () {
    Route::get('/kunjungan/index', 'KunjunganController@index')->name('kunjungan.index');
    Route::get('/kunjungan/pagelist', 'KunjunganController@PageListKunjungan')->name('kunjungan.pagelist');
    Route::post('/kunjungan/kirimnomor', 'KunjunganController@KirimNomorAntrian')->name('kunjungan.kirimnomor');
    Route::post('/kunjungan/kirimlinkfeedback', 'KunjunganController@KirimLinkFeedback')->name('kunjungan.kirimlinkfeedback');
    Route::post('/kunjungan/kirimlinkskd', 'KunjunganController@KirimLinkSKD')->name('kunjungan.kirimlinkskd');
    Route::post('/kunjungan/mulai', 'KunjunganController@MulaiLayanan')->name('kunjungan.mulai');
    Route::post('/kunjungan/akhir', 'KunjunganController@AkhirLayanan')->name('kunjungan.akhir');
    Route::post('/kunjungan/tindaklanjut', 'KunjunganController@TindakLanjutSave')->name('tindaklanjut.save');
    Route::post('/kunjungan/flagantrian', 'KunjunganController@FlagAntrianUpdate')->name('flagantrian.update');
    Route::post('/tujuanbaru/save', 'KunjunganController@TujuanBaruSave')->name('tujuanbaru.save');
    Route::post('/jeniskunjungansave', 'KunjunganController@JenisKunjunganSave')->name('jeniskunjungan.save');
    Route::get('/kunjungan/printantrian/{uid}', 'KunjunganController@PrintNomorAntrian')->name('kunjungan.printantrian');

    Route::get('/kunjungan/tambah', 'KunjunganController@tambah')->name('kunjungan.tambah');
    Route::post('/kunjungan/simpan', 'KunjunganController@simpan')->name('kunjungan.simpan');
    Route::post('/kunjungan/hapus', 'KunjunganController@HapusKunjungan')->name('kunjungan.hapus');
    //webapi
    Route::get('/webapi', 'WebapiController@WebApi')->name('webapi');
    //whatsapp
    Route::get('/whatsapp', 'WhatsappController@WhatsappList')->name('whatsapp');
    Route::post('/cron/notif', 'WhatsappController@NotifJaga')->name('cron.notif');
    Route::post('/wa/import', 'WhatsappController@WhatsappImport')->name('wa.import');
    //timeline
    Route::get('/timeline/{uid}', 'KunjunganController@Timeline')->name('timeline');
});
Route::group(['middleware' => ['auth']], function () {
    //Petugas
    Route::post('/kunjungan/petugassimpan', 'KunjunganController@PetugasSimpan')->name('kunjungan.petugassimpan');
    Route::get('/petugas/index', 'PetugasController@index')->name('petugas.index');
    Route::post('/petugas/simpan', 'PetugasController@simpan')->name('petugas.simpan');
    Route::post('/petugas/hapus', 'PetugasController@hapus')->name('petugas.hapus');
    Route::get('/petugas/pagelist', 'PetugasController@PageListPetugas')->name('petugas.pagelist');
    Route::post('/petugas/ubahflag', 'PetugasController@UbahFlagMember')->name('petugas.ubahflag');
    Route::post('/petugas/admgantipasswd', 'PetugasController@AdmGantiPasswd')->name('petugas.admgantipasswd');
    Route::post('/petugas/updatedata', 'PetugasController@UpdateMemberData')->name('petugas.updatedata');
});
