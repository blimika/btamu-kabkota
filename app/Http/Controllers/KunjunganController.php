<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pendidikan;
use App\Kunjungan;
use App\Tujuan;
use App\LayananPst;
use App\LayananKantor;
use App\Tanggal;
use App\Whatsapp;
use App\Pengunjung;
use Carbon\Carbon;
use App\Helpers\Generate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Mail\KirimAntrian;

class KunjunganController extends Controller
{
    public function tambah()
    {
        $Pendidikan = Pendidikan::orderBy('pendidikan_kode', 'asc')->get();
        $Tujuan = Tujuan::orderBy('tujuan_kode', 'asc')->get();
        $LayananPst = LayananPst::where('layanan_pst_kode','<','99')->orderBy('layanan_pst_kode', 'asc')->get();
        $LayananKantor = LayananKantor::orderBy('layanan_kantor_kode', 'asc')->get();
        return view('kunjungan.tambah',[
            'Pendidikan' => $Pendidikan,
            'LayananPst'=>$LayananPst,
            'LayananKantor'=>$LayananKantor,
            'Tujuan'=>$Tujuan
            ]);
    }
    public function simpan(Request $request)
    {
        //dd($request->all());
        /*
        "_token" => "jp1T2XBSVwXZszpQdYRyaRDxjPaNWzDhdyOsmyjr"
        "pengunjung_id" => null
        "pengunjung_uid" => null
        "edit_pengunjung" => "0"
        "pengunjung_baru" => "1"
        "validasi_nomorhp" => "1"
        "nomor_hp" => "081237802900"
        "pengunjung_nama" => "putu mika"
        "pengunjung_jk" => "laki_laki"
        "pengunjung_tahun_lahir" => "1982"
        "pengunjung_pekerjaan" => "mahasiswa"
        "pengunjung_pendidikan" => "3"
        "pengunjung_email" => "mika@statsntb.id"
        "pengunjung_alamat" => "mataram"
        "kunjungan_tujuan" => "2"
        "layananpst_kode" => "1"
        "layanan_kantor_kode" => null
        "kunjungan_keperluan" => "cari publikasi terbaru"
        "jenis_kunjungan" => "1"
        "jumlah_tamu" => "1"
        "tamu_laki" => "0"
        "tamu_wanita" => "0"
        "foto" => "data:image/png;base64,iVBORw
        */
        $waktu_hari_ini = date('Ymd_His');
        //cek nomor hp pengunjung
        //data pengunjung
        $CekNomorHp = Pengunjung::where('pengunjung_nomor_hp',$request->nomor_hp)->first();
        if ($request->pengunjung_baru == 1 && !$CekNomorHp)
        {
            //pengunjung baru
            $pengunjung_uid = Generate::Kode(6);
            $data = new Pengunjung();
            $data->pengunjung_uid = $pengunjung_uid;
            $data->pengunjung_nama = $request->pengunjung_nama;
            $data->pengunjung_nomor_hp = $request->nomor_hp;
            $data->pengunjung_tahun_lahir = $request->pengunjung_tahun_lahir;
            $data->pengunjung_jenis_kelamin = $request->pengunjung_jk;
            $data->pengunjung_pekerjaan = $request->pengunjung_pekerjaan;
            $data->pengunjung_pendidikan = $request->pengunjung_pendidikan;
            $data->pengunjung_email = $request->pengunjung_email;
            $data->pengunjung_alamat = $request->pengunjung_alamat;
            $data->pengunjung_total_kunjungan = 0;
            //$data->pengunjung_user_uid = 0; kedepan untuk member
            $data->save();
            ///simpan foto
            $pengunjung_id = $data->pengunjung_id;
            if (preg_match('/^data:image\/(\w+);base64,/', $request->foto)) {
                $namafile_kunjungan = '/img/kunjungan/kunjungan_' . $pengunjung_uid . '_' . $waktu_hari_ini . '.png';
                $namafile_profil = '/img/profil/pengunjung_' . $pengunjung_uid . '.png';
                $data_foto = substr($request->foto, strpos($request->foto, ',') + 1);
                $data_foto = base64_decode($data_foto);
                Storage::disk('public')->put($namafile_kunjungan, $data_foto);
                Storage::disk('public')->put($namafile_profil, $data_foto);
                //update link foto
                $data->pengunjung_foto_profil = $namafile_profil;
                $data->update();
                //batas update
            }
            else {
                $namafile_kunjungan = NULL;
                $namafile_profil = NULL;
            }
        }
        else
        {
            //data pengunjung sudah ada
            //apakah di update apa tidak
            //define foto kunjungan dulu
            if (preg_match('/^data:image\/(\w+);base64,/', $request->foto)) {
                $namafile_kunjungan = '/img/kunjungan/kunjungan_' . $request->pengunjung_uid . '_' . $waktu_hari_ini . '.png';
                $namafile_profil = '/img/profil/pengunjung_' . $request->pengunjung_uid . '.png';
                $data_foto = substr($request->foto, strpos($request->foto, ',') + 1);
                $data_foto = base64_decode($data_foto);
                Storage::disk('public')->put($namafile_kunjungan, $data_foto);
                Storage::disk('public')->put($namafile_profil, $data_foto);
            } else {
                $namafile_kunjungan = NULL;
                $namafile_profil = NULL;
            }
            //apakah edit pengunjung
            $data = Pengunjung::where('pengunjung_uid',$request->pengunjung_uid)->first();
            if ($request->edit_pengunjung == 1)
            {
                //kalo di edit
                $data->pengunjung_nama = $request->pengunjung_nama;
                $data->pengunjung_nomor_hp = $request->nomor_hp;
                $data->pengunjung_tahun_lahir = $request->pengunjung_tahun_lahir;
                $data->pengunjung_jenis_kelamin = $request->pengunjung_jk;
                $data->pengunjung_pekerjaan = $request->pengunjung_pekerjaan;
                $data->pengunjung_pendidikan = $request->pengunjung_pendidikan;
                $data->pengunjung_email = $request->pengunjung_email;
                $data->pengunjung_alamat = $request->pengunjung_alamat;
            }

            if ($namafile_profil != NULL) {
                $data->pengunjung_foto_profil = $namafile_profil;
            }
            $data->update();
        }
        //batas pengunjung
        //tambahkan tabel kunjungan
        //cek kunjungan dulu apakah sudah pernah
        $cek_kunjungan = Kunjungan::where([['pengunjung_uid', $data->pengunjung_uid], ['kunjungan_tanggal', Carbon::today()->format('Y-m-d')], ['kunjungan_tujuan', $request->kunjungan_tujuan]])->count();
        if ($cek_kunjungan > 0) {
            //sudah ada kasih info kalo sudah mengisi
            $pesan_error = 'Data pengunjung ' . $data->pengunjung_nama . ' sudah pernah mengisi bukutamu hari tanggal ' . Carbon::today()->isoFormat('dddd, D MMMM Y');
            $warna_error = 'danger';
        }
        else
        {
            //masukkan ke tabel kunjungan
            //cek dulu antrian ada sesuai layanan
            //kalo pst cek layanan pst juga
            //mulai tujuan
            if ($request->kunjungan_tujuan == 1)
            {
                //kantor
                $data_antrian = Kunjungan::where([['kunjungan_tanggal', Carbon::today()->format('Y-m-d')],['kunjungan_tujuan',$request->kunjungan_tujuan], ['kunjungan_layanan_kantor', $request->layanan_kantor_kode]])->orderBy('kunjungan_nomor_antrian', 'desc')->first();
                $data_layanan_utama = LayananKantor::where('layanan_kantor_kode',$request->layanan_kantor_kode)->first();
                $nomor_antrian_inisial = $data_layanan_utama->layanan_kantor_inisial;
                $layanan_pst = 99;
                $layanan_kantor = $request->layanan_kantor_kode;
            }
            elseif ($request->kunjungan_tujuan == 2)
            {
                //pst
                $data_antrian = Kunjungan::where([['kunjungan_tanggal', Carbon::today()->format('Y-m-d')],['kunjungan_tujuan',$request->kunjungan_tujuan], ['kunjungan_layanan_pst', $request->layananpst_kode]])->orderBy('kunjungan_nomor_antrian', 'desc')->first();
                $data_layanan_utama = LayananPst::where('layanan_pst_kode',$request->layananpst_kode)->first();
                $nomor_antrian_inisial = $data_layanan_utama->layanan_pst_inisial;
                $layanan_pst = $request->layananpst_kode;
                $layanan_kantor = 99;
            }
            else
            {
                //selain kantor dan pst
                $data_antrian = Kunjungan::where([['kunjungan_tanggal', Carbon::today()->format('Y-m-d')], ['kunjungan_tujuan',$request->kunjungan_tujuan]])->orderBy('kunjungan_nomor_antrian', 'desc')->first();
                $layanan_pst = 99;
                $layanan_kantor = 99;
                $data_layanan_utama = Tujuan::where('tujuan_kode',$request->kunjungan_tujuan)->first();
                $nomor_antrian_inisial = $data_layanan_utama->tujuan_inisial;
            }
            //batas tujuan
            //mulai antrian
            if ($data_antrian) {
                //kalo sudah ada antrian
                $nomor_selanjutnya = $data_antrian->kunjungan_nomor_antrian + 1;
            }
            else {
                //belum ada sama sekali
                $nomor_selanjutnya = 1;
            }
            //batas antrian
            //proses jenis kunjungan perorangan / kelompok
            if ($request->jenis_kunjungan == 2) {
                $jumlah_tamu = $request->jumlah_tamu;
                $laki = $request->tamu_laki;
                $wanita = $request->tamu_wanita;
            }
            else
            {
                $jumlah_tamu = 1;
                //cek jenis kelamin ambil dari query data diatas
                if ($data->pengunjung_jenis_kelamin == "laki_laki") {
                    $laki = 1;
                    $wanita = 0;
                } else {
                    $laki = 0;
                    $wanita = 1;
                }
            }
            //batas jenis kunjungan
            //tambahkan ke tabel m_kunjungan
            $newdata = new Kunjungan();
            $newdata->pengunjung_uid = $data->pengunjung_uid;
            $newdata->kunjungan_uid = Generate::Kode(7);
            $newdata->kunjungan_tanggal = Carbon::today()->format('Y-m-d');
            $newdata->kunjungan_keperluan = $request->kunjungan_keperluan;
            $newdata->kunjungan_jenis = $request->jenis_kunjungan;
            $newdata->kunjungan_tujuan = $request->kunjungan_tujuan;
            $newdata->kunjungan_layanan_pst = $layanan_pst;
            $newdata->kunjungan_layanan_kantor = $layanan_kantor;
            if ($namafile_kunjungan != NULL) {
                $newdata->kunjungan_foto = $namafile_kunjungan;
            }
            $newdata->kunjungan_jumlah_orang = $jumlah_tamu;
            $newdata->kunjungan_jumlah_pria = $laki;
            $newdata->kunjungan_jumlah_wanita = $wanita;
            $newdata->kunjungan_nomor_antrian = $nomor_selanjutnya;
            $newdata->kunjungan_teks_antrian = $nomor_antrian_inisial . '-' . sprintf("%03d", $nomor_selanjutnya);
            $newdata->save();
            //tambah total kunjungan di tabel pengunjung
            //ambil dari tabel m_pengunjung
            $total_kunjungan = $data->pengunjung_total_kunjungan;
            $data->pengunjung_total_kunjungan = $total_kunjungan + 1;
            $data->update();
            //batasan
            //inisiasi email
            if ($newdata->kunjungan_tujuan == 1)
            {
                $layanan = $newdata->Tujuan->tujuan_nama .' - '. $newdata->LayananKantor->layanan_kantor_nama;
            }
            elseif ($newdata->kunjungan_tujuan == 2)
            {
                $layanan = $newdata->Tujuan->tujuan_nama .' - '. $newdata->LayananPst->layanan_pst_nama;
            }
            else
            {
                $layanan = $newdata->Tujuan->tujuan_nama;
            }
            $body = new \stdClass();
            $body->kunjungan_uid = $newdata->kunjungan_uid;
            $body->pengunjung_nama = $newdata->Pengunjung->pengunjung_nama;
            $body->pengunjung_email = $newdata->Pengunjung->pengunjung_email;
            $body->pengunjung_nomor_hp = $newdata->Pengunjung->pengunjung_nomor_hp;
            $body->kunjungan_tanggal = \Carbon\Carbon::parse($newdata->created_at)->isoFormat('dddd, D MMMM Y');
            $body->layanan = $layanan;
            $body->nomor_antrian = $newdata->kunjungan_teks_antrian;
            $body->nama_aplikasi = ENV('NAMA_APLIKASI');
            $body->nama_satker = ENV('NAMA_SATKER');
            $body->alamat_satker = ENV('ALAMAT_SATKER');
            //cek email valid apa tidak
            if (filter_var($newdata->Pengunjung->pengunjung_email, FILTER_VALIDATE_EMAIL))
            {
                if (ENV('APP_KIRIM_MAIL') == true) {
                    Mail::to($newdata->Pengunjung->pengunjung_email)->send(new KirimAntrian($body));
                }
                //batas
            }
            //batasan email
            //notif whatsapp dilengkapi kemudian
            Session::flash('message_header', "<strong>Terimakasih</strong>");
            $pesan_error = "Data kunjungan an. <strong><i>" . trim($request->pengunjung_nama) . "</i></strong> berhasil ditambahkan";
            $warna_error = "success";
        }
        Session::flash('message', $pesan_error);
        Session::flash('message_type', $warna_error);
        return redirect()->route('depan');
    }
}
