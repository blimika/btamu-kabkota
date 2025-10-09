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
Route::get('/k/f/{uid}', 'KunjunganController@NewFeedback')->name('kunjungan.feedback');
Route::get('/kunjungan/tambah', 'KunjunganController@tambah')->name('kunjungan.tambah');
Route::post('/kunjungan/simpan', 'KunjunganController@simpan')->name('kunjungan.simpan');
Route::get('/permintaan/tambah', 'KunjunganController@TambahPermintaan')->name('permintaan.tambah');
Route::post('/permintaan/simpan', 'KunjunganController@simpanPermintaan')->name('permintaan.simpan');
//webapi
Route::get('/webapi', 'WebapiController@WebApi')->name('webapi');
Route::group(['middleware' => 'ip.or.login'], function () {
    //whatsapp
    Route::get('/whatsapp', 'WhatsappController@WhatsappList')->name('whatsapp');
    Route::post('/whatsapp/notifikasi', 'WhatsappController@NotifJaga')->name('petugas.notifikasi');
    Route::post('/wa/import', 'WhatsappController@WhatsappImport')->name('wa.import');
    //timeline
    Route::get('/timeline/{uid}', 'PengunjungController@Timeline')->name('timeline');
});
Route::group(['middleware' => ['auth']], function () {
    //Petugas
    Route::post('/kunjungan/hapus', 'KunjunganController@HapusKunjungan')->name('kunjungan.hapus');
    Route::get('/kunjungan/index', 'KunjunganController@index')->name('kunjungan.index');
    Route::get('/kunjungan/laporan', 'KunjunganController@laporan')->name('kunjungan.laporan');
    Route::get('/kunjungan/pagelist', 'KunjunganController@PageListKunjungan')->name('kunjungan.pagelist');
    Route::post('/kunjungan/kirimnomor', 'KunjunganController@KirimNomorAntrian')->name('kunjungan.kirimnomor');
    Route::post('/kunjungan/kirimlinkfeedback', 'KunjunganController@KirimLinkFeedback')->name('kunjungan.kirimlinkfeedback');
    Route::post('/pengunjung/kirimlinkskd', 'PengunjungController@KirimLinkSKD')->name('pengunjung.kirimlinkskd');
    Route::post('/kunjungan/mulai', 'KunjunganController@MulaiLayanan')->name('kunjungan.mulai');
    Route::post('/kunjungan/akhir', 'KunjunganController@AkhirLayanan')->name('kunjungan.akhir');
    Route::post('/kunjungan/tindaklanjut', 'KunjunganController@TindakLanjutSave')->name('tindaklanjut.save');
    Route::post('/kunjungan/flagantrian', 'KunjunganController@FlagAntrianUpdate')->name('flagantrian.update');
    Route::post('/tujuanbaru/save', 'KunjunganController@TujuanBaruSave')->name('tujuanbaru.save');
    Route::post('/jeniskunjungansave', 'KunjunganController@JenisKunjunganSave')->name('jeniskunjungan.save');
    Route::get('/k/p/{uid}', 'KunjunganController@PrintNomorAntrian')->name('kunjungan.printantrian');
    Route::post('/kunjungan/petugassimpan', 'KunjunganController@PetugasSimpan')->name('kunjungan.petugassimpan');
    Route::get('/petugas/index', 'PetugasController@index')->name('petugas.index');
    Route::post('/petugas/simpan', 'PetugasController@simpan')->name('petugas.simpan');
    Route::post('/petugas/hapus', 'PetugasController@hapus')->name('petugas.hapus');
    Route::get('/petugas/pagelist', 'PetugasController@PageListPetugas')->name('petugas.pagelist');
    Route::post('/petugas/ubahflag', 'PetugasController@UbahFlag')->name('petugas.ubahflag');
    Route::post('/petugas/admgantipasswd', 'PetugasController@AdmGantiPasswd')->name('petugas.admgantipasswd');
    Route::post('/petugas/updatedata', 'PetugasController@UpdatePetugasData')->name('petugas.updatedata');
    Route::get('/petugas/penilaian', 'PetugasController@Penilaian')->name('petugas.nilai');
    Route::get('/petugas/format', 'PetugasController@FormatPetugas')->name('petugas.format');
    Route::post('/petugas/import', 'PetugasController@ImportPetugas')->name('petugas.import');
    Route::get('/petugas/profil', 'PetugasController@profil')->name('petugas.profil');
    Route::post('/petugas/updateprofil', 'PetugasController@UpdateProfil')->name('petugas.updateprofil');
    Route::post('/petugas/gantipassword', 'PetugasController@GantiPassword')->name('petugas.gantipassword');
    //Tanggal dan jadwal
    Route::get('/master/tanggal', 'MasterController@tanggal')->name('master.tanggal');
    Route::get('/master/kalendar', 'MasterController@kalendar')->name('master.kalendar');
    Route::get('/master/format/jadwal', 'MasterController@FormatJadwal')->name('master.formatjadwal');
    Route::get('/master/listtanggal', 'MasterController@PageListTanggal')->name('master.listtanggal');
    Route::post('/master/gen/tanggal', 'MasterController@GenerateTanggal')->name('master.gentanggal');
    Route::post('/master/updatetgl', 'MasterController@UpdateTanggal')->name('master.updatetgl');
    Route::post('/master/updatejadwal', 'MasterController@UpdateJadwal')->name('master.updatejadwal');
    Route::post('/master/import/jadwal', 'MasterController@ImportJadwalPetugas')->name('master.importjadwal');
    Route::get('/master/tujuan', 'MasterController@tujuan')->name('master.tujuan');
    Route::get('/master/listtujuan', 'MasterController@PageListTujuan')->name('master.listtujuan');
    Route::post('/master/simpantujuan', 'MasterController@SimpanTujuan')->name('master.simpantujuan');
    Route::post('/master/hapustujuan', 'MasterController@HapusTujuan')->name('master.hapustujuan');
    Route::post('/master/updatetujuan', 'MasterController@updateTujuan')->name('master.updatetujuan');
    Route::get('/master/akses', 'MasterController@akses')->name('master.akses');
    Route::get('/master/listakses', 'MasterController@PageListAkses')->name('master.listakses');
    Route::post('/master/hapusakses', 'MasterController@HapusAkses')->name('master.hapusakses');
    Route::post('/master/ubahflagakses', 'MasterController@UbahFLagAkses')->name('master.ubahflagakses');
    Route::post('/master/simpanakses', 'MasterController@SimpanAkses')->name('master.simpanakses');
    Route::post('/master/updateakses', 'MasterController@UpdateAkses')->name('master.updateakses');
    //pengunjung
    Route::get('/pengunjung/index', 'PengunjungController@index')->name('pengunjung.index');
    Route::get('/pengunjung/pagelist', 'PengunjungController@PageList')->name('pengunjung.pagelist');
    Route::get('/pengunjung/feedback', 'PengunjungController@feedback')->name('pengunjung.feedback');
    Route::post('/pengunjung/update', 'PengunjungController@update')->name('pengunjung.update');
    Route::post('/pengunjung/hapus', 'PengunjungController@hapus')->name('pengunjung.hapus');
    Route::get('/pengunjung/pagelistfeedback', 'PengunjungController@PageListFeedback')->name('pengunjung.pagelistfeedback');
    //whatsapp
    Route::get('/whatsapp/index', 'WhatsappController@index')->name('whatsapp.index');
    //data
    Route::get('/data/index', 'DataController@index')->name('data.index');
});
