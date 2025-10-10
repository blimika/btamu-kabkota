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

class DataController extends Controller
{
    public function index()
    {
        $pengunjung = Pengunjung::count();
        $kunjungan = Kunjungan::count();
        $petugas = User::count();
        return view('data.index',[
            'pengunjung' => $pengunjung,
            'kunjungan' => $kunjungan,
            'petugas' => $petugas,
        ]);
    }
    /*
    kode import
    insert into m_pengunjung (pengunjung_uid,pengunjung_nama,pengunjung_nomor_hp,pengunjung_tahun_lahir, pengunjung_jenis_kelamin, pengunjung_pekerjaan, pengunjung_pendidikan, pengunjung_email,pengunjung_alamat,pengunjung_foto_profil,created_at, updated_at)
select pengunjung_uid,pengunjung_nama,pengunjung_nomor_hp,pengunjung_tahun_lahir, pengunjung_jenis_kelamin, pengunjung_pekerjaan, pengunjung_pendidikan, pengunjung_email,pengunjung_alamat,pengunjung_foto_profil,created_at, updated_at from m_pengunjung_lama;

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
}
