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
use App\Mail\KirimFeedback;
use App\Mail\KirimLinkSKD;
use PDF;
use Excel;
use App\Services\WhatsAppService;
use App\Exports\FormatJadwal;
use App\Imports\ImportJadwalPetugas;
use Illuminate\Support\Facades\Schema;

class DataController extends Controller
{
    public function index()
    {
        $pengunjung = Pengunjung::count();
        $kunjungan = Kunjungan::count();
        $petugas = User::count();
        //cek tabel petugas (users), pengunjung (m_pengunjung), m_new_kunjungan
        if (Schema::hasTable('m_petugas')) {
            $petugas_old = DB::table('m_petugas')->count();
        }
        else
        {
            $petugas_old = 0;
        }
        if (Schema::hasTable('m_new_kunjungan')) {
            $kunjungan_old = DB::table('m_new_kunjungan')->count();
        }
        else
        {
            $kunjungan_old = 0;
        }
        if (Schema::hasTable('pengunjung')) {
            $pengunjung_old = DB::table('pengunjung')->count();
        }
        else
        {
            $pengunjung_old = 0;
        }
        //dd($petugas_old);
        return view('data.index',[
            'pengunjung' => $pengunjung,
            'kunjungan' => $kunjungan,
            'petugas' => $petugas,
            'pengunjung_old' => $pengunjung_old,
            'kunjungan_old' => $kunjungan_old,
            'petugas_old' => $petugas_old,
        ]);
    }
    /*
    kode import
sebelum insert
1. ALTER TABLE `m_pengunjung_lama` ADD `pengunjung_jenis_kelamin` ENUM('laki_laki','perempuan') NOT NULL DEFAULT 'laki_laki' AFTER `pengunjung_jk`;
    insert into m_pengunjung (pengunjung_uid,pengunjung_nama,pengunjung_nomor_hp,pengunjung_tahun_lahir, pengunjung_jenis_kelamin, pengunjung_pekerjaan, pengunjung_pendidikan, pengunjung_email,pengunjung_alamat,pengunjung_foto_profil,created_at, updated_at)
select pengunjung_uid,pengunjung_nama,pengunjung_nomor_hp,pengunjung_tahun_lahir, pengunjung_jenis_kelamin, pengunjung_pekerjaan, pengunjung_pendidikan, pengunjung_email,pengunjung_alamat,pengunjung_foto_profil,created_at, updated_at from m_pengunjung_lama;

sebelum insert
1. ALTER TABLE `m_new_kunjungan` ADD `kunjungan_jenis_baru` ENUM('perorangan','kelompok') NOT NULL DEFAULT 'perorangan' AFTER `kunjungan_jenis`;
2. ALTER TABLE `m_new_kunjungan` ADD `kunjungan_flag_feedback_baru` ENUM('belum','sudah') NOT NULL DEFAULT 'belum' AFTER `kunjungan_flag_feedback`;
3. ALTER TABLE `m_new_kunjungan` ADD `kunjungan_flag_antrian_baru` ENUM('ruang_tunggu','dalam_layanan','selesai') NOT NULL DEFAULT 'ruang_tunggu' AFTER `kunjungan_flag_antrian`;
4. ALTER TABLE `m_kunjungan` ADD `kunjungan_petugas_username` VARCHAR(50) NULL DEFAULT NULL AFTER `kunjungan_petugas_uid`;
5. update m_new_kunjungan set kunjungan_jenis_baru='kelompok' where kunjungan_jenis='2';
6. update m_new_kunjungan set kunjungan_flag_feedback_baru='sudah' where kunjungan_flag_feedback='2';
7. update m_new_kunjungan set kunjungan_flag_antrian_baru='dalam_layanan' where kunjungan_flag_antrian='2';
8. update m_new_kunjungan set kunjungan_flag_antrian_baru='selesai' where kunjungan_flag_antrian='3';
9. update m_kunjungan set kunjungan_petugas_username='admin' where kunjungan_petugas_uid is null;


    INSERT INTO `m_kunjungan` (`kunjungan_id`, `pengunjung_uid`, `kunjungan_uid`, `kunjungan_tanggal`, `kunjungan_keperluan`, `kunjungan_tindak_lanjut`, `kunjungan_jenis`, `kunjungan_tujuan`, `kunjungan_layanan_pst`, `kunjungan_layanan_kantor`, `kunjungan_foto`, `kunjungan_jumlah_orang`, `kunjungan_jumlah_pria`, `kunjungan_jumlah_wanita`, `kunjungan_flag_feedback`, `kunjungan_nilai_feedback`, `kunjungan_komentar_feedback`, `kunjungan_ip_feedback`, `kunjungan_agent_feedback`, `kunjungan_tanggal_feedback`, `kunjungan_nomor_antrian`, `kunjungan_teks_antrian`, `kunjungan_loket_petugas`, `kunjungan_flag_antrian`, `kunjungan_jam_datang`, `kunjungan_jam_pulang`, `kunjungan_petugas_username`, `created_at`, `updated_at`)
select `kunjungan_id`, `pengunjung_uid`, `kunjungan_uid`, `kunjungan_tanggal`, `kunjungan_keperluan`, `kunjungan_tindak_lanjut`, `kunjungan_jenis_baru`, `kunjungan_tujuan`, `kunjungan_pst`, `kunjungan_kantor`, `kunjungan_foto`, `kunjungan_jumlah_orang`, `kunjungan_jumlah_pria`, `kunjungan_jumlah_wanita`, `kunjungan_flag_feedback_baru`, `kunjungan_nilai_feedback`, `kunjungan_komentar_feedback`, `kunjungan_ip_feedback`, `kunjungan_agent_feedback`, `kunjungan_tanggal_feedback`, `kunjungan_nomor_antrian`, `kunjungan_teks_antrian`, `kunjungan_loket_petugas`, `kunjungan_flag_antrian_baru`, `kunjungan_jam_datang`, `kunjungan_jam_pulang`, `kunjungan_petugas_username`, `created_at`, `updated_at` from m_new_kunjungan;
    */


    public function SinkronPetugas(Request $request)
    {
        ///jumlah kunjungan masing2 petugas & nama petugas
        if (Auth::user()->user_level == 'admin')
        {
            $data = Pengunjung::get();
            if ($data)
            {
                $i=0;
                foreach ($data as $item) {
                    $data_kunjungan = Kunjungan::where('pengunjung_uid', $item->pengunjung_uid)->count();
                    $item->pengunjung_total_kunjungan = $data_kunjungan;
                    $item->update();
                    $i++;
                }

            }
            $base_kunjungan = Kunjungan::get();
            if ($base_kunjungan)
            {
                $j=0;
                $je=0;
                $j_sudah=0;
                foreach ($base_kunjungan as $item_base) {
                    if ($item_base->kunjungan_petugas_uid == "")
                    {
                        $data_petugas = User::where('username',$item_base->kunjungan_petugas_username)->first();
                        if ($data_petugas)
                        {
                            $item_base->kunjungan_petugas_uid = $data_petugas->user_uid;
                            $item_base->kunjungan_sarpras_feedback = $item_base->kunjungan_nilai_feedback;
                            $item_base->update();
                            $j++;
                        }
                        else
                        {
                            $je++;
                        }
                    }
                    else
                    {
                        $j_sudah++;
                    }

                }
            }

            $arr = array(
                'status' => true,
                'message' => "data pengunjung sebanyak ".$i." dan sinkron petugas sebanyak ".$j." sudah di sinkronisasi, error ".$je." tidak di sinkron ".$j_sudah
            );
        }
        else
        {
            $arr = array(
                'status' => false,
                'message' => "tidak mempunyai hak untuk sinkronisasi pengunjung"
            );
        }
        return Response()->json($arr);
    }
    public function SinkronPetugasProvinsi(Request $request)
    {
        if (Auth::user()->user_level == 'admin')
        {
            $data_petugas = DB::table('m_petugas')->where('level','>','1')->get();
            $i = 0;
            $e = 0;
            foreach ($data_petugas as $item) {
                $cek_username = User::where('username',$item->username)->first();
                if ($cek_username)
                {
                    //sudah ada
                    $e++;
                }
                else
                {
                    $data = new User();
                    $data->user_uid = Generate::Kode(6);
                    $data->username = $item->username;
                    $data->name = $item->name;
                    $data->email = $item->email;
                    $data->user_telepon = $item->telepon;
                    $data->password = $item->password;
                    if ($item->flag == 1)
                    {
                        $data->user_flag = 'aktif';
                    }
                    if ($item->level == 20)
                    {
                        $data->user_level = 'admin';
                    }
                    else
                    {
                        $data->user_level = 'operator';
                    }
                    $data->user_last_login = $item->lastlogin;
                    $data->user_last_ip = $item->lastip;
                    $data->user_foto = $item->user_foto;
                    if ($item->created_at == null)
                    {
                        $data->created_at = NOW();
                        $data->updated_at = NOW();
                    }
                    else
                    {
                        $data->created_at = $item->created_at;
                        $data->updated_at = $item->updated_at;
                    }

                    $data->save();
                    $i++;
                }
            }

            $arr = array(
                'status' => true,
                'message' => "Data petugas lama sebanyak ".$i." akun sudah disinkron ke sistem baru"
            );
        }
        else
        {
            $arr = array(
                'status' => false,
                'message' => "tidak mempunyai hak untuk sinkronisasi petugas"
            );
        }
        return Response()->json($arr);
    }
    public function SinkronPengunjungProvinsi(Request $request)
    {
        if (Auth::user()->user_level == 'admin')
        {
            $data_pengunjung = DB::table('pengunjung')->get();
            $i = 0;
            $e = 0;
            foreach ($data_pengunjung as $item) {
                $cek_nomor_hp = Pengunjung::where('pengunjung_nomor_hp',$item->pengunjung_nomor_hp)->first();
                if ($cek_nomor_hp)
                {
                    //sudah ada
                    $e++;
                }
                else
                {
                    $data = new Pengunjung();
                    $data->pengunjung_uid = $item->pengunjung_uid;
                    $data->pengunjung_nama = $item->pengunjung_nama;
                    $data->pengunjung_nomor_hp = $item->pengunjung_nomor_hp;
                    $data->pengunjung_tahun_lahir = $item->pengunjung_tahun_lahir;
                    $data->pengunjung_jenis_kelamin = $item->pengunjung_jk;
                    $data->pengunjung_pekerjaan = $item->pengunjung_pekerjaan;
                    $data->pengunjung_pendidikan = $item->pengunjung_pendidikan;
                    $data->pengunjung_email = $item->pengunjung_email;
                    $data->pengunjung_alamat = $item->pengunjung_alamat;
                    $data->pengunjung_foto_profil = $item->pengunjung_foto_profil;
                    $data->pengunjung_total_kunjungan = $item->pengunjung_total_kunjungan;
                    $data->created_at = $item->created_at;
                    $data->updated_at = $item->updated_at;
                    $data->save();
                    $i++;
                }
            }

            $arr = array(
                'status' => true,
                'message' => "Data pengunjung lama sebanyak ".$i." akun sukses sudah disinkron dan ".$e." akun tidak di sinkron ke sistem baru"
            );
        }
        else
        {
            $arr = array(
                'status' => false,
                'message' => "tidak mempunyai hak untuk sinkronisasi pengunjung"
            );
        }
        return Response()->json($arr);
    }
    public function SinkronKunjunganProvinsi(Request $request)
    {
        if (Auth::user()->user_level == 'admin')
        {
            $data_kunjungan = DB::table('m_new_kunjungan')->get();
            $i = 0;
            $e = 0;
            foreach ($data_kunjungan as $item) {
                $cek_pengunjung = Pengunjung::where('pengunjung_uid',$item->pengunjung_uid)->first();
                if ($cek_pengunjung)
                {
                    //ada
                    $petugas = User::where('username',$item->kunjungan_petugas_username)->first();
                    if ($petugas)
                    {
                        $petugas_uid = $petugas->user_uid;
                        $petugas_username = $petugas->username;
                    }
                    else
                    {
                        $petugas = User::where('username','admin')->first();
                        $petugas_uid = $petugas->user_uid;
                        $petugas_username = $petugas->username;
                    }
                    $cek_kunjungan = Kunjungan::where('kunjungan_uid',$item->kunjungan_uid)->first();
                    if ($cek_kunjungan)
                    {
                        $e++;
                    }
                    else
                    {
                        $data = new Kunjungan();
                        $data->pengunjung_uid = $item->pengunjung_uid;
                        $data->kunjungan_uid  = $item->kunjungan_uid;
                        $data->kunjungan_tanggal = $item->kunjungan_tanggal;
                        $data->kunjungan_keperluan = $item->kunjungan_keperluan;
                        $data->kunjungan_tindak_lanjut = $item->kunjungan_tindak_lanjut;
                        if ($item->kunjungan_jenis == '1') {
                            $data->kunjungan_jenis = 'perorangan';
                        }
                        else
                        {
                            $data->kunjungan_jenis = 'kelompok';
                        }
                        $data->kunjungan_tujuan = $item->kunjungan_tujuan;
                        $data->kunjungan_layanan_pst = $item->kunjungan_pst;
                        $data->kunjungan_layanan_kantor = $item->kunjungan_kantor;
                        $data->kunjungan_foto = $item->kunjungan_foto;
                        $data->kunjungan_jumlah_orang = $item->kunjungan_jumlah_orang;
                        $data->kunjungan_jumlah_pria = $item->kunjungan_jumlah_pria;
                        $data->kunjungan_jumlah_wanita = $item->kunjungan_jumlah_wanita;
                        if ($item->kunjungan_flag_feedback == '1') {
                            $data->kunjungan_flag_feedback = 'belum';
                        } else {
                            $data->kunjungan_flag_feedback = 'sudah';
                        }
                        $data->kunjungan_nilai_feedback = $item->kunjungan_nilai_feedback;
                        $data->kunjungan_sarpras_feedback = $item->kunjungan_nilai_feedback;
                        $data->kunjungan_komentar_feedback = $item->kunjungan_komentar_feedback;
                        $data->kunjungan_ip_feedback = $item->kunjungan_ip_feedback	;
                        $data->kunjungan_agent_feedback = $item->kunjungan_agent_feedback;
                        $data->kunjungan_tanggal_feedback = $item->kunjungan_tanggal_feedback;
                        $data->kunjungan_nomor_antrian = $item->kunjungan_nomor_antrian;
                        $data->kunjungan_teks_antrian = $item->kunjungan_teks_antrian;
                        if ($item->kunjungan_flag_antrian == 1)
                        {
                            $flag_antrian = 'ruang_tunggu';
                        }
                        elseif ($item->kunjungan_flag_antrian == 2)
                        {
                            $flag_antrian = 'dalam_layanan';
                        }
                        else
                        {
                            $flag_antrian = 'selesai';
                        }
                        $data->kunjungan_flag_antrian = $flag_antrian;
                        $data->kunjungan_jam_datang = $item->kunjungan_jam_datang;
                        $data->kunjungan_jam_pulang = $item->kunjungan_jam_pulang;
                        $data->kunjungan_petugas_username = $petugas_username;
                        $data->kunjungan_petugas_uid = $petugas_uid;
                        $data->created_at = $item->created_at;
                        $data->updated_at = $item->updated_at;
                        $data->save();
                        $i++;
                    }
                }
                else
                {
                    $e++;
                }
            }

            $arr = array(
                'status' => true,
                'message' => "Data kunjungan lama sebanyak ".$i." akun sukses sudah disinkron dan ".$e." akun tidak di sinkron ke sistem baru"
            );
        }
        else
        {
            $arr = array(
                'status' => false,
                'message' => "tidak mempunyai hak untuk sinkronisasi pengunjung"
            );
        }
        return Response()->json($arr);
    }
}
