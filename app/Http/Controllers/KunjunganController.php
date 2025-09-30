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

class KunjunganController extends Controller
{
    protected $whatsappService;
    protected $cek_nomor_hp;
    protected $link_skd;
    protected $nama_aplikasi;
    protected $nama_satker;
    protected $alamat_satker;
    protected $link_feedback;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->link_skd = env('APP_LINK_SKD');
        $this->nama_aplikasi = ENV('NAMA_APLIKASI');
        $this->nama_satker = ENV('NAMA_SATKER');
        $this->alamat_satker = ENV('ALAMAT_SATKER');
        $this->whatsappService = $whatsappService;
        $this->link_feedback = ENV('APP_URL').'/k/f/';
    }
    private function cek_nomor_hp($nomor)
    {
        // Mengecek apakah nomor diawali dengan '0'
        if (substr($nomor, 0, 1) === '0') {
            // Jika ya, kembalikan nomor tanpa awalan '0'
            $nomorhp = "62".substr($nomor, 1);

        }
        // Mengecek apakah nomor diawali dengan '+'
        elseif (substr($nomor, 0, 1) === '+') {
            // Jika ya, abaikan nomor ini dengan mengembalikan null
            // Anda bisa mengubahnya untuk menampilkan pesan error atau lainnya
            $nomorhp = substr($nomor, 1);
        }
        // Jika tidak diawali '0' atau '+'
        else {
            $nomorhp = $nomor;
        }
        return $nomorhp;
    }
    public function tambah()
    {
        //cek hari dulu
        $cek_hari = Tanggal::where('tanggal_angka', Carbon::today()->format('Y-m-d'))->first();
        if ($cek_hari->tanggal_jenis == 'kerja' or ENV('APP_CEK_LIBUR') == false) {
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
        else
        {
            return view('kunjungan.libur', ['tanggal' => $cek_hari]);
        }
    }
    public function simpan(Request $request)
    {
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
            $header_error = '<strong>Error!</strong>';
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
            $body->nama_aplikasi = $this->nama_aplikasi;
            $body->nama_satker = $this->nama_satker;
            $body->alamat_satker = $this->alamat_satker;
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
            //kirim notif ke pengunjung
            if (ENV('APP_WA_LOKAL_MODE') == true) {
                //persiapan untuk WA
                $recipients = $newdata->Pengunjung->pengunjung_nomor_hp;
                $recipients = $this->cek_nomor_hp($recipients);
                $message = '#Hai *'.$body->pengunjung_nama.'*'.chr(10).
                'Terimakasih, telah berkunjung ke BPS Provinsi Nusa Tenggara Barat.'.chr(10)
                .'Berikut nomor antrian Anda!'.chr(10).chr(10)
                .'#Detil Kunjungan'.chr(10)
                .'UID : *'.$body->kunjungan_uid.'*'.chr(10)
                .'Nama : *'.$body->pengunjung_nama.'*'.chr(10)
                .'Email : *'.$body->pengunjung_email.'*'.chr(10)
                .'Nomor HP : *'.$body->pengunjung_nomor_hp.'*'.chr(10)
                .'Tanggal Kunjungan : *'.$body->kunjungan_tanggal.'* '.chr(10).chr(10)
                .'Layanan :  *'.$body->layanan.'*'.chr(10)
                .'# Nomor Antrian : *'.$body->nomor_antrian.'*'.chr(10).chr(10)
                .'Terimakasih,'.chr(10)
                .$this->nama_aplikasi.chr(10)
                .$this->nama_satker.chr(10)
                .$this->alamat_satker;
                //input ke log pesan
                $new_wa = new Whatsapp();
                $new_wa->wa_tanggal = Carbon::today()->format('Y-m-d');
                $new_wa->wa_uid = Generate::Kode(8);
                $new_wa->wa_pengunjung_uid = $data->pengunjung_uid;
                $new_wa->wa_kunjungan_uid = $newdata->kunjungan_uid;
                $new_wa->wa_target = $recipients;
                $new_wa->wa_message = $message;
                $new_wa->save();
                try {
                    $result = $this->whatsappService->sendMessage($recipients, $message);
                    //return response()->json($result);
                    if ($result)
                    {
                        $new_wa->wa_message_id = $result['results']['message_id'];
                        $new_wa->wa_status = $result['results']['status'];
                        $new_wa->wa_flag = 'terkirim';
                        $new_wa->update();
                    }
                    //$arr = $result;
                } catch (\Throwable $e) {
                    $error = Log::error('WA LOKAL: ' . $e->getMessage());
                    //return response()->json(['error' => 'Internal Server Error'],500);
                    $new_wa->wa_status = $error ;
                    $new_wa->wa_flag = 'gagal';
                    $new_wa->update();
                }
            }
            //kirim notif ke petugas jaga
            //jika kunjungan ke PST
            if ($request->kunjungan_tujuan == 2)
            {
                if (ENV('APP_WA_PETUGAS_MODE') == true) {
                    $petugas_jaga = Tanggal::where('tanggal_angka', Carbon::today()->format('Y-m-d'))->first();
                    if ($petugas_jaga)
                    {
                        if ($petugas_jaga->tanggal_jenis == 'kerja')
                        {
                            $msg_operator = 'Ada kunjungan ke '.$this->nama_satker.chr(10)
                            .'Berikut informasinya:'.chr(10).chr(10)
                            .'#Detil Kunjungan'.chr(10)
                            .'Pengunjung UID : *'.$data->pengunjung_uid.'*'.chr(10)
                            .'Kunjungan UID : *'.$body->kunjungan_uid.'*'.chr(10)
                            .'Nama : *'.$body->pengunjung_nama.'*'.chr(10)
                            .'Email : *'.$body->pengunjung_email.'*'.chr(10)
                            .'Nomor HP : *'.$body->pengunjung_nomor_hp.'*'.chr(10)
                            .'Tanggal Kunjungan : *'.$body->kunjungan_tanggal.'* '.chr(10)
                            .'Keperluan : *'.$request->kunjungan_keperluan.'*'.chr(10).chr(10)
                            .'Layanan :  *'.$body->layanan.'*'.chr(10)
                            .'# Nomor Antrian : *'.$body->nomor_antrian.'*'.chr(10).chr(10)
                            .'Terimakasih,'.chr(10)
                            .$this->nama_aplikasi.chr(10)
                            .$this->nama_satker.chr(10)
                            .$this->alamat_satker;
                            //penjaga pst 1
                            if ($petugas_jaga->tanggal_petugas1_uid != null)
                            {
                                if (ENV('APP_WA_LOKAL_MODE') == true) {
                                    $hp_petugas1 = $petugas_jaga->Petugas1->user_telepon;
                                    $recipients1 = $this->cek_nomor_hp($hp_petugas1);
                                    $new_wa1 = new Whatsapp();
                                    $new_wa1->wa_tanggal = Carbon::today()->format('Y-m-d');
                                    $new_wa1->wa_uid = Generate::Kode(8);
                                    $new_wa1->wa_pengunjung_uid = $data->pengunjung_uid;
                                    $new_wa1->wa_kunjungan_uid = $body->kunjungan_uid;
                                    $new_wa1->wa_target = $recipients1;
                                    $new_wa1->wa_message = $msg_operator;
                                    $new_wa1->save();
                                    try {
                                        $result1 = $this->whatsappService->sendMessage($recipients1, $msg_operator);
                                        if ($result1)
                                        {
                                            $new_wa1->wa_message_id = $result1['results']['message_id'];
                                            $new_wa1->wa_status = $result1['results']['status'];
                                            $new_wa1->wa_flag = 'terkirim';
                                            $new_wa1->update();
                                        }

                                    } catch (\Throwable $e) {
                                        $error1 = Log::error('WA LOKAL 1: ' . $e->getMessage());
                                        $new_wa1->wa_status = $error1 ;
                                        $new_wa1->wa_flag = 'gagal';
                                        $new_wa1->update();
                                    }
                                }
                            }
                            sleep(1);
                            if ($petugas_jaga->tanggal_petugas2_uid != null)
                            {
                                if (ENV('APP_WA_LOKAL_MODE') == true) {
                                    $hp_petugas2 = $petugas_jaga->Petugas2->user_telepon;
                                    $recipients2 = $this->cek_nomor_hp($hp_petugas2);
                                    $new_wa2 = new Whatsapp();
                                    $new_wa2->wa_tanggal = Carbon::today()->format('Y-m-d');
                                    $new_wa2->wa_uid = Generate::Kode(8);
                                    $new_wa2->wa_pengunjung_uid = $data->pengunjung_uid;
                                    $new_wa2->wa_kunjungan_uid = $body->kunjungan_uid;
                                    $new_wa2->wa_target = $recipients1;
                                    $new_wa2->wa_message = $msg_operator;
                                    $new_wa2->save();
                                    try {
                                        $result2 = $this->whatsappService->sendMessage($recipients2, $msg_operator);
                                        if ($result2)
                                        {
                                            $new_wa2->wa_message_id = $result2['results']['message_id'];
                                            $new_wa2->wa_status = $result2['results']['status'];
                                            $new_wa2->wa_flag = 'terkirim';
                                            $new_wa2->update();
                                        }

                                    } catch (\Throwable $e) {
                                        $error2 = Log::error('WA LOKAL 2: ' . $e->getMessage());
                                        $new_wa2->wa_status = $error2 ;
                                        $new_wa2->wa_flag = 'gagal';
                                        $new_wa2->update();
                                    }
                                }
                            }
                            //penjaga pst 2
                        }
                    }
                }
            }
            $header_error = "<strong>Terimakasih</strong>";
            $pesan_error = "Data kunjungan an. <strong><i>" . trim($request->pengunjung_nama) . "</i></strong> berhasil ditambahkan";
            $warna_error = "success";
        }
        Session::flash('message_header', $header_error);
        Session::flash('message', $pesan_error);
        Session::flash('message_type', $warna_error);
        return redirect()->route('depan');
    }
    public function FeedbackSave(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data tidak di simpan'
        );
        if ($request->feedback_nilai == "")
        {
            //balikin nilai masih kosong
            $arr = array(
                'status'=>false,
                'message'=>'Nilai rating belum diberikan, silakan ulangi lagi'
            );
        }
        else
        {
            $data = Kunjungan::where('kunjungan_uid',$request->kunjungan_uid)->first();
            if ($data)
            {
                $data->kunjungan_flag_feedback = 'sudah';
                $data->kunjungan_nilai_feedback = $request->feedback_nilai;
                $data->kunjungan_komentar_feedback = $request->feedback_komentar;
                $data->kunjungan_ip_feedback = $request->getClientIp();
                $data->kunjungan_tanggal_feedback = now();
                $data->update();

                $arr = array(
                    'status'=>true,
                    'message'=>'Feedback an. '.$data->Pengunjung->pengunjung_nama.' sudah tersimpan',
                    'data'=>true
                );
            }
        }
        return Response()->json($arr);
    }
    public function DisplayAntrian()
    {
        $data_antrian = Kunjungan::where([['kunjungan_tanggal',Carbon::now()->format('Y-m-d')],['kunjungan_flag_antrian','dalam_layanan']])->orderBy('kunjungan_loket_petugas','asc')->take(2)->get();
        //dd($data_antrian_terakhir);
        if (count($data_antrian) > 0)
        {
            $data1 = array(
                "loket_status" => true,
                "loket_petugas" => $data_antrian[0]['kunjungan_loket_petugas'],
                "nomor_antrian"=> $data_antrian[0]['kunjungan_teks_antrian'],
            );
            if (count($data_antrian) > 1)
            {
                $data2 = array(
                    "loket_status" => true,
                    "loket_petugas" => $data_antrian[1]['kunjungan_loket_petugas'],
                    "nomor_antrian"=> $data_antrian[1]['kunjungan_teks_antrian'],
                );
            }
            else
            {
                $data2 = array(
                    "loket_status" => false,
                    "loket_petugas" => '-',
                    "nomor_antrian"=> '-',
                );
            }
        }
        else
        {
            $data1 = array(
                "loket_status" => false,
                "loket_petugas" => '-',
                "nomor_antrian"=> '-',
            );
            $data2 = array(
                "loket_status" => false,
                "loket_petugas" => '-',
                "nomor_antrian"=> '-',
            );
        }

        //dd($data1, $data2);
        return view('kunjungan.display',['data1'=>$data1,'data2'=>$data2]);
    }
    public function index()
    {
        $Tujuan = Tujuan::orderBy('tujuan_kode', 'asc')->get();
        $LayananPst = LayananPst::orderBy('layanan_pst_kode', 'asc')->get();
        $LayananKantor = LayananKantor::orderBy('layanan_kantor_kode', 'asc')->get();
        $flag_antrian = array(
            array('kode' => 'ruang_tunggu', 'nama' => 'Ruang Tunggu'),
            array('kode' => 'dalam_layanan', 'nama' => 'Dalam Layanan'),
            array('kode' => 'selesai', 'nama' => 'Selesai'),
        );
        //dd($flag_antrian);
        $DataPetugas = User::where('user_flag','aktif')->get();
        $PetugasJaga = Tanggal::where('tanggal_angka', Carbon::today()->format('Y-m-d'))->first();
        return view('kunjungan.index',['master_flag_antrian'=>$flag_antrian,'MasterTujuan'=>$Tujuan,'MasterLayananPST'=>$LayananPst,'MasterLayananKantor'=>$LayananKantor,'DataPetugas'=>$DataPetugas,'PetugasJaga'=>$PetugasJaga]);
    }
    public function PageListKunjungan(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Kunjungan::count();
        //total record searching
        $totalRecordswithFilter =  DB::table('m_kunjungan')
        ->leftJoin('m_pengunjung', 'm_kunjungan.pengunjung_uid', '=', 'm_pengunjung.pengunjung_uid')
        ->leftJoin('m_tujuan', 'm_kunjungan.kunjungan_tujuan', '=', 'm_tujuan.tujuan_kode')
        ->leftJoin('users', 'm_kunjungan.kunjungan_petugas_uid', '=', 'users.user_uid')
        ->leftJoin('m_layanan_kantor', 'm_kunjungan.kunjungan_layanan_kantor', '=', 'm_layanan_kantor.layanan_kantor_kode')
        ->leftJoin('m_layanan_pst', 'm_kunjungan.kunjungan_layanan_pst', '=', 'm_layanan_pst.layanan_pst_kode')
        ->leftJoin('m_pendidikan', 'm_pengunjung.pengunjung_pendidikan', '=', 'm_pendidikan.pendidikan_kode')
        ->when($searchValue, function ($q) use ($searchValue) {
            return $q->where('pengunjung_nama', 'like', '%' . $searchValue . '%')
                     ->orWhere('kunjungan_keperluan', 'like', '%' . $searchValue . '%')
                     ->orWhere('kunjungan_uid', 'like', '%' . $searchValue . '%')
                     ->orWhere('kunjungan_tanggal', 'like', '%' . $searchValue . '%')
                     ->orWhere('users.name', 'like', '%' . $searchValue . '%')
                     ->orWhere('m_layanan_pst.layanan_pst_nama', 'like', '%' . $searchValue . '%')
                     ->orWhere('m_layanan_kantor.layanan_kantor_nama', 'like', '%' . $searchValue . '%')
                     ->orWhere('kunjungan_teks_antrian', 'like', '%' . $searchValue . '%');
        })->count();

        // Fetch records
        $records = DB::table('m_kunjungan')
        ->leftJoin('m_pengunjung', 'm_kunjungan.pengunjung_uid', '=', 'm_pengunjung.pengunjung_uid')
        ->leftJoin('m_tujuan', 'm_kunjungan.kunjungan_tujuan', '=', 'm_tujuan.tujuan_kode')
        ->leftJoin('users', 'm_kunjungan.kunjungan_petugas_uid', '=', 'users.user_uid')
        ->leftJoin('m_layanan_kantor', 'm_kunjungan.kunjungan_layanan_kantor', '=', 'm_layanan_kantor.layanan_kantor_kode')
        ->leftJoin('m_layanan_pst', 'm_kunjungan.kunjungan_layanan_pst', '=', 'm_layanan_pst.layanan_pst_kode')
        ->leftJoin('m_pendidikan', 'm_pengunjung.pengunjung_pendidikan', '=', 'm_pendidikan.pendidikan_kode')
            ->when($searchValue, function ($q) use ($searchValue) {
                return $q->where('pengunjung_nama', 'like', '%' . $searchValue . '%')
                     ->orWhere('kunjungan_keperluan', 'like', '%' . $searchValue . '%')
                     ->orWhere('kunjungan_uid', 'like', '%' . $searchValue . '%')
                     ->orWhere('kunjungan_tanggal', 'like', '%' . $searchValue . '%')
                     ->orWhere('users.name', 'like', '%' . $searchValue . '%')
                     ->orWhere('m_layanan_pst.layanan_pst_nama', 'like', '%' . $searchValue . '%')
                     ->orWhere('m_layanan_kantor.layanan_kantor_nama', 'like', '%' . $searchValue . '%')
                     ->orWhere('kunjungan_teks_antrian', 'like', '%' . $searchValue . '%');
            })
            ->select('m_kunjungan.*', 'm_pengunjung.pengunjung_nama','m_pengunjung.pengunjung_email', 'm_pengunjung.pengunjung_jenis_kelamin', 'm_tujuan.tujuan_inisial', 'm_tujuan.tujuan_nama', 'users.name', 'users.username', 'm_layanan_kantor.layanan_kantor_nama','m_layanan_pst.layanan_pst_nama', 'm_pendidikan.pendidikan_nama')
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName, $columnSortOrder)
            ->orderBy('created_at','desc')
            ->get();
            //inisiasi aawal
            $data_arr = array();
            foreach ($records as $item) {
                //link feedback
                if ($item->kunjungan_flag_feedback == 'sudah')
                {
                    //sudah isi feedback
                    $kirim_link_feedback = '';
                }
                else
                {

                    $kirim_link_feedback = '<a class="dropdown-item kirimlinkfeedback" href="#" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '" data-email="' . $item->pengunjung_email.'" data-toggle="tooltip" title="Kirim Link Feedback">Kirim Link Feedback</a>';
                }
                //link tindak lanjut dan ganti petugas
                if ($item->kunjungan_flag_antrian == 'ruang_tunggu')
                {
                    $link_tindaklanjut_ganti_petugas = "";
                }
                else
                {
                    $link_tindaklanjut_ganti_petugas = '
                     <a class="dropdown-item" href="#" data-id="' . $item->kunjungan_id . '"data-uid="' . $item->kunjungan_uid . '" data-puid="' . $item->pengunjung_uid . '" data-nama="' . $item->pengunjung_nama . '" data-toggle="modal" data-target="#EditTindakLanjutModal"><span data-toggle="tooltip" title="Edit tindak lanjut kunjungan an. '.$item->pengunjung_nama.'">Edit Tindak Lanjut</span></a>
                    <a class="dropdown-item" href="#" data-uid="' . $item->kunjungan_uid . '" data-toggle="modal" data-target="#EditPetugasModal"><span data-toggle="tooltip" title="Edit Petugas kunjungan an. '.$item->pengunjung_nama.'">Edit Petugas</span></a>
                    ';
                }
                //tombol aksi
                $aksi = '
            <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="ti-settings"></i>
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="#" data-uid="' . $item->kunjungan_uid . '" data-toggle="modal" data-target="#ViewKunjunganModal">View</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="'.route("kunjungan.printantrian",$item->kunjungan_uid).'" target="_blank" data-toggle="tooltip" title="Print Nomor Antrian">Print Antrian</a>
                <a class="dropdown-item kirimnomorantrian" href="#" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '" data-email="' . $item->pengunjung_email.'" data-toggle="tooltip" title="Kirim Nomor Antrian">Kirim Antrian</a>
                <div class="dropdown-divider"></div>
                <div class="dropdown-divider"></div>'.
                $link_tindaklanjut_ganti_petugas
                .'<a class="dropdown-item" href="#" data-toggle="modal" data-target="#EditTujuanModal" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '">Ubah Tujuan</a>
                <a class="dropdown-item" href="#" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-jenis="'.$item->kunjungan_jenis.'" data-nama="' . $item->pengunjung_nama . '" data-toggle="modal" data-target="#EditJenisKunjunganModal">Ubah Jenis</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '" data-toggle="modal" data-target="#EditFlagAntrianModal">Flag Antrian</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item kirimlinkskd" href="#" data-puid="' . $item->pengunjung_uid . '" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '" data-email="' . $item->pengunjung_email.'" data-toggle="tooltip" title="Kirim Link SKD">Kirim Link SKD</a>
                '.$kirim_link_feedback.'
                <a class="dropdown-item copyurlfeedback" target="_blank" href="'.route('kunjungan.feedback',$item->kunjungan_uid).'" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '">Copy Link Feedback</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item hapuskunjungan" href="#" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '" data-tanggal="'.$item->kunjungan_tanggal.'">Delete</a>
                    </div>
                </div>
                ';
                //batas tombol aksi
                if ($item->kunjungan_tujuan == 1)
                {
                    //ke kantor
                    $tujuan = $item->layanan_kantor_nama;
                    if ($item->kunjungan_layanan_kantor == 1)
                    {
                        $warna_layanan_utama = 'badge-warning';
                    }
                    else if ($item->kunjungan_layanan_kantor == 2)
                    {
                        $warna_layanan_utama = 'badge-info';
                    }
                    else if ($item->kunjungan_layanan_kantor == 3)
                    {
                        $warna_layanan_utama = 'badge-success';
                    }
                    else
                    {
                        $warna_layanan_utama = 'badge-primary';
                    }

                }
                elseif ($item->kunjungan_tujuan == 2)
                {
                    //ke pst ambil layanan pst
                    $tujuan = $item->layanan_pst_nama;

                    if ($item->kunjungan_layanan_pst == 1)
                    {
                        $warna_layanan_utama = 'badge-success';
                    }
                    else if ($item->kunjungan_layanan_pst == 2)
                    {
                        $warna_layanan_utama = 'badge-warning';
                    }
                    else if ($item->kunjungan_layanan_pst == 3)
                    {
                        $warna_layanan_utama = 'badge-info';
                    }
                    else if ($item->kunjungan_layanan_pst == 4)
                    {
                        $warna_layanan_utama = 'badge-primary';
                    }
                    else
                    {
                        $warna_layanan_utama = 'badge-primary';
                    }
                }
                else
                {
                    //nama layanan aja
                    $tujuan = $item->tujuan_nama;
                    if ($item->kunjungan_tujuan == 1)
                    {
                        $warna_layanan_utama = 'badge-danger';
                    }
                    else if ($item->kunjungan_tujuan == 2)
                    {
                        $warna_layanan_utama = 'badge-success';
                    }
                    else if ($item->kunjungan_tujuan == 3)
                    {
                        $warna_layanan_utama = 'badge-warning';
                    }
                    else if ($item->kunjungan_tujuan == 4)
                    {
                        $warna_layanan_utama = 'badge-info';
                    }
                    else
                    {
                        $warna_layanan_utama = 'badge-primary';
                    }
                }
                //batas
                //warna layanan utama
                $layanan_utama = '<span class="badge '.$warna_layanan_utama.' badge-pill">'.$tujuan.'</span>';
                //warna flag antrian
                if ($item->kunjungan_flag_antrian == 'ruang_tunggu')
                {
                    $warna_flag_antrian = 'badge-danger';
                    $tombol_feedback='';
                }
                else if ($item->kunjungan_flag_antrian == 'dalam_layanan')
                {
                    $warna_flag_antrian = 'badge-warning';
                    $tombol_feedback='';
                }
                else
                {
                    $warna_flag_antrian = 'badge-success';
                    if ($item->kunjungan_flag_feedback == 'sudah')
                    {
                        if ($item->kunjungan_komentar_feedback == "")
                        {
                            $warna_komentar_feedback = 'btn-info';
                        }
                        else
                        {
                            $warna_komentar_feedback = 'btn-success';
                        }
                        $tombol_feedback = '<button type="button" class="btn btn-rounded '.$warna_komentar_feedback.' btn-xs m-t-5" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '" data-tanggal="' . $item->kunjungan_tanggal . '" data-toggle="modal" data-target="#ViewFeedbackModal"><span data-toggle="tooltip" data-placement="top" title="Sudah memberikan feedback"><i class="fas fa-check-circle"></i> feedback</span></button>';
                    }
                    else
                    {
                        $tombol_feedback = '<button type="button" class="btn btn-rounded btn-danger btn-xs tombolfeedback m-t-5" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '" data-tanggal="' . $item->kunjungan_tanggal . '" data-toggle="modal" data-target="#BeriFeebackModal"><span data-toggle="tooltip" data-placement="top" title="Belum memberikan feedback"><i class="fas fa-question"></i> feedback</span></button>';
                    }
                }
                //batas
                //flag antrian
                    $flag_antrian_teks = '<span class="badge '.$warna_flag_antrian.' badge-pill">'.$item->kunjungan_flag_antrian.'</span>';
                    //batas flag antrian
                    //waktu datang dan waktu pulang
                    if ($item->kunjungan_jam_datang == "") {
                        $mulai = '<button type="button" class="btn btn-circle btn-success btn-sm mulailayanan" data-toggle="tooltip" data-placement="top" title="Mulai memberikan layanan" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '" data-tanggal="' . $item->kunjungan_tanggal . '"><i class="fas fa-hand-holding-heart"></i></button>';
                    }
                    else {
                        $mulai = '<span class="badge badge-info badge-pill">' . Carbon::parse($item->kunjungan_jam_datang)->format('H:i') . '</span>';
                    }
                    if ($item->kunjungan_jam_pulang == "") {
                        if ($item->kunjungan_jam_datang != "") {
                            $akhir = '<button type="button" class="btn btn-circle btn-danger btn-sm akhirlayanan" data-toggle="tooltip" data-placement="top" title="Mengakhiri pemberian layanan" data-id="' . $item->kunjungan_id . '" data-uid="' . $item->kunjungan_uid . '" data-nama="' . $item->pengunjung_nama . '" data-tanggal="' . $item->kunjungan_tanggal . '"><i class="fas fa-sign-out-alt"></i></button>';
                        } else {
                            $akhir = '';
                        }
                    } else {
                        $akhir = '<span class="badge badge-success badge-pill">' . Carbon::parse($item->kunjungan_jam_pulang)->format('H:i') . '</span>';
                    }
                //batas
                //petugas
                    //petugas
                    if ($item->kunjungan_petugas_uid != "") {
                        if ($item->kunjungan_loket_petugas == 1)
                        {
                            $loket_petugas = '<span class="badge badge-success badge-pill">Petugas '.$item->kunjungan_loket_petugas.'</span>';
                        }
                        else
                        {
                            $loket_petugas = '<span class="badge badge-info badge-pill">Petugas '.$item->kunjungan_loket_petugas.'</span>';
                        }
                        $petugas = $item->name .'<br />'.Generate::RatingPetugas($item->kunjungan_petugas_uid).'<br />'. $loket_petugas;
                    }
                    else {
                        $petugas = '<span class="badge badge-danger badge-pill">belum ada</span';
                    }
                //batas petugas
                //jenis kelamin
                if ($item->pengunjung_jenis_kelamin == 'laki_laki') {
                    $jk = '<span class="badge badge-info badge-pill">Laki-Laki</span>';
                }
                else {
                    $jk = '<span class="badge badge-danger badge-pill">Perempuan</span>';
                }
                //jenis kunjungan 1 perorangan 2 kelompok
                if ($item->kunjungan_jenis == 'perorangan') {
                    $kunjungan_jenis = '<span class="badge badge-info badge-pill">Perorangan</span>';
                } else {
                    $kunjungan_jenis = '<span class="badge badge-warning badge-pill">Kelompok ('. $item->kunjungan_jumlah_orang . ' org)</span> <span class="badge badge-info badge-pill">L ' . $item->kunjungan_jumlah_pria . '</span> <span class="badge badge-danger badge-pill">P ' . $item->kunjungan_jumlah_wanita . '</span>';
                }
                //tujuan
                if ($item->kunjungan_tujuan == 1) {
                    $warna_tujuan = 'badge-danger';
                }
                elseif ($item->kunjungan_tujuan == 2)
                {
                    $warna_tujuan = 'badge-success';
                }
                elseif ($item->kunjungan_tujuan == 3)
                {
                    $warna_tujuan = 'badge-warning';
                }
                elseif ($item->kunjungan_tujuan == 4)
                {
                    $warna_tujuan = 'badge-info';
                }
                elseif ($item->kunjungan_tujuan == 5)
                {
                    $warna_tujuan = 'badge-dark';
                }
                else {
                    $warna_tujuan = 'badge-primary';
                }
                $tujuan = '<span class="badge '.$warna_tujuan.' badge-pill">' . $item->tujuan_inisial . '</span>';
                //batas
                 //tindak lanjut more
                if (strlen($item->kunjungan_tindak_lanjut) > 80)
                {
                    $tindak_lanjut = '<div id="dots">'.Str::limit($item->kunjungan_tindak_lanjut,80).'
                    </div>
                    <div id="moreTeks">'.$item->kunjungan_tindak_lanjut.'</div>
                    <button class="m-t-5 m-b-5 btn btn-xs btn-info btnMore" id="btnMore">more</button>
                    ';
                }
                else
                {
                    $tindak_lanjut = $item->kunjungan_tindak_lanjut;
                }
                //keperluan more
                if (strlen($item->kunjungan_keperluan) > 80)
                {
                    $keperluan = '<div id="dots">'.Str::limit($item->kunjungan_keperluan,80).'
                    </div>
                    <div id="moreTeks">'.$item->kunjungan_keperluan.'</div>
                    <button class="m-t-5 m-b-5 btn btn-xs btn-info btnMore" id="btnMore">more</button>
                    ';
                }
                else
                {
                    $keperluan = $item->kunjungan_keperluan;
                }
                $nama ='<a class="text-black" href="#" data-uid="' . $item->kunjungan_uid . '" data-toggle="modal" data-target="#ViewKunjunganModal">'.$item->pengunjung_nama.'</a>';
                //batas
                if ($item->kunjungan_tujuan < 3)
                {
                    $kunj_keperluan = $keperluan .'<br />'.$tujuan .'<br />'. $layanan_utama .'<br />'.$kunjungan_jenis;
                }
                else
                {
                    $kunj_keperluan = $keperluan .'<br />'. $layanan_utama .'<br />'.$kunjungan_jenis;
                }
                $data_arr[] = array(
                    "kunjungan_uid" => $item->kunjungan_uid,
                    "pengunjung_nama" =>  $nama.'<br />'.$jk,
                    "kunjungan_tanggal" => $item->kunjungan_tanggal,
                    "kunjungan_keperluan" => $kunj_keperluan,
                    "kunjungan_tindak_lanjut" => $tindak_lanjut,
                    "kunjungan_tujuan" => $layanan_utama,
                    "kunjungan_teks_antrian" => $item->kunjungan_teks_antrian .'<br />'.$flag_antrian_teks,
                    "kunjungan_jam_datang" => $mulai,
                    "kunjungan_jam_pulang" => $akhir,
                    "kunjungan_petugas_uid" => $petugas .'<br />'.$tombol_feedback,
                    "kunjungan_created_at"=>$item->created_at,
                    "aksi" => $aksi
                );

            };

            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                "aaData" => $data_arr
            );
            echo json_encode($response);
            exit;
    }
    public function KirimNomorAntrian(Request $request)
    {
        $data = Kunjungan::where('kunjungan_uid', $request->uid)->first();
        $arr = array(
            'status' => false,
            'message' => 'Nomor antrian tidak ditemukan'
        );
        if ($data) {
            if ($data->kunjungan_tujuan == 1)
            {
                $layanan = $data->Tujuan->tujuan_nama .' - '. $data->LayananKantor->layanan_kantor_nama;
            }
            elseif ($data->kunjungan_tujuan == 2)
            {
                $layanan = $data->Tujuan->tujuan_nama .' - '. $data->LayananPst->layanan_pst_nama;
            }
            else
            {
                $layanan = $data->Tujuan->tujuan_nama;
            }
            //kirim mail
            $body = new \stdClass();
            $body->kunjungan_uid = $data->kunjungan_uid;
            $body->pengunjung_nama = $data->Pengunjung->pengunjung_nama;
            $body->pengunjung_email = $data->Pengunjung->pengunjung_email;
            $body->pengunjung_nomor_hp = $data->Pengunjung->pengunjung_nomor_hp;
            $body->kunjungan_tanggal = \Carbon\Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y');
            $body->layanan = $layanan;
            $body->nomor_antrian = $data->kunjungan_teks_antrian;
            $body->nama_aplikasi = $this->nama_aplikasi;
            $body->nama_satker = $this->nama_satker;
            $body->alamat_satker = $this->alamat_satker;
            //cek email valid apa tidak
            if (filter_var($data->Pengunjung->pengunjung_email, FILTER_VALIDATE_EMAIL))
            {
               if (ENV('APP_KIRIM_MAIL') == true) {
                    Mail::to($data->Pengunjung->pengunjung_email)->send(new KirimAntrian($body));

                    $arr = array(
                        'status' => true,
                        'message' => 'Nomor Antrian an. '.$data->Pengunjung->pengunjung_nama.' sudah dikirim ke alamat email '.$data->Pengunjung->pengunjung_email
                    );
                }
                else
                {
                    $arr = array(
                        'status' => false,
                        'message' => 'APP_KIRIM_MAIL bernilai False di .env'
                    );
                }
                //batas
            }
            else
            {
                $arr = array(
                    'status' => false,
                    'message' => 'Alamat email Kunjungan an. '.$data->Pengunjung->pengunjung_nama.' tidak sesuai format'
                );
            }
            //persiapan untuk WA
            if (ENV('APP_WA_LOKAL_MODE') == true) {
                //persiapan untuk WA
                $recipients = $data->Pengunjung->pengunjung_nomor_hp;
                $recipients = $this->cek_nomor_hp($recipients);
                $message = '#Hai *'.$body->pengunjung_nama.'*'.chr(10)
                .'Terimakasih, telah berkunjung ke BPS Provinsi Nusa Tenggara Barat.'.chr(10)
                .'Berikut nomor antrian Anda!'.chr(10).chr(10)
                .'#Detil Kunjungan'.chr(10)
                .'UID : *'.$body->kunjungan_uid.'*'.chr(10)
                .'Nama : *'.$body->pengunjung_nama.'*'.chr(10)
                .'Email : *'.$body->pengunjung_email.'*'.chr(10)
                .'Nomor HP : *'.$body->pengunjung_nomor_hp.'*'.chr(10)
                .'Tanggal Kunjungan : *'.$body->kunjungan_tanggal.'* '.chr(10).chr(10)
                .'Layanan :  *'.$body->layanan.'*'.chr(10)
                .'# Nomor Antrian : *'.$body->nomor_antrian.'*'.chr(10).chr(10)
                .'Terimakasih,'.chr(10)
                .$this->nama_aplikasi.chr(10)
                .$this->nama_satker.chr(10)
                .$this->alamat_satker;
                //input ke log pesan
                $new_wa = new Whatsapp();
                $new_wa->wa_tanggal = Carbon::today()->format('Y-m-d');
                $new_wa->wa_uid = Generate::Kode(8);
                $new_wa->wa_pengunjung_uid = $data->pengunjung_uid;
                $new_wa->wa_kunjungan_uid = $data->kunjungan_uid;
                $new_wa->wa_target = $recipients;
                $new_wa->wa_message = $message;
                $new_wa->save();
                try {
                    $result = $this->whatsappService->sendMessage($recipients, $message);
                    //return response()->json($result);
                    if ($result)
                    {
                        $new_wa->wa_message_id = $result['results']['message_id'];
                        $new_wa->wa_status = $result['results']['status'];
                        $new_wa->wa_flag = 'terkirim';
                        $new_wa->update();
                    }
                    //$arr = $result;
                } catch (\Throwable $e) {
                    $error = Log::error('WA LOKAL: ' . $e->getMessage());
                    //return response()->json(['error' => 'Internal Server Error'],500);
                    $new_wa->wa_status = $error ;
                    $new_wa->wa_flag = 'gagal';
                    $new_wa->update();
                }
            }
        }
        #dd($request->all());
        return Response()->json($arr);
    }
    public function PrintNomorAntrian($uid)
    {
        $data = Kunjungan::where('kunjungan_uid',$uid)->first();
        if ($data)
        {
            PDF::setOptions(['dpi' => 150, 'defaultFont' => 'Helvetica','isHtml5ParserEnabled'=>true]);
            $pdf = PDF::loadView('kunjungan.print',compact('data'))->setPaper('A7');
            $nama=strtoupper($data->Pengunjung->pengunjung_nama);
            return $pdf->stream('Antrian'.$nama.'_Nomor_Antrian_'.$data->kunjungan_teks_antrian.'.pdf');
        }
    }
    public function NewFeedback($uid)
    {
        $data = Kunjungan::where('kunjungan_uid',$uid)->first();
        return view('feedback.index',['data'=>$data]);
    }
    public function MulaiLayanan(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data tidak tersedia'
        );
        if (Auth::user())
        {
            //hanya operator / admin yg bisa klik ini
            $data = Kunjungan::where([['kunjungan_uid', $request->uid], ['kunjungan_jam_datang', NULL]])->first();
            if ($data)
            {
                //cek dulu petugas ini lagi melayani tidak
                //kalo tidak melayani
                //cek loket petugas1 dan 2 pada tanggal itu flag_antrian kode 2 tidak
                $cek = Kunjungan::where([['kunjungan_tanggal',Carbon::now()->format("Y-m-d")],['kunjungan_flag_antrian','dalam_layanan'],['kunjungan_petugas_uid',Auth::user()->user_uid]])->first();
                if ($cek)
                {
                    //ada pengunjung ternyata
                    $arr = array(
                        'status' => false,
                        'message' => 'Masih ada pengunjung yang dilayani, silakan diselesaikan dulu'

                    );
                }
                else
                {
                   //cek apakah tujuan pst / tidak
                   //kalo pst cek loket
                   //selain itu langsung taruh loket sesuai kode tujuan
                   if ($data->kunjungan_tujuan == 2)
                    {
                        $cek_jumlah_loket = Kunjungan::where([['kunjungan_tanggal',Carbon::now()->format("Y-m-d")],['kunjungan_flag_antrian','dalam_layanan'],['kunjungan_tujuan',2]])->get();
                        if ($cek_jumlah_loket->count() == 2)
                        {
                            $arr = array(
                                'status' => false,
                                'message' => 'Semua loket petugas masih ada pengunjung dilayani, tunggu setelah selesai dilayani'

                            );
                        }
                        elseif ($cek_jumlah_loket->count() == 1)
                        {
                            //hanya ada 1 loket, cek loket mana yg dipakai
                            foreach ($cek_jumlah_loket as $item) {
                                $loket_aktif = $item->kunjungan_loket_petugas;
                            }
                            if ($loket_aktif == 1)
                            {
                                $loket_petugas = 2;
                            }
                            else
                            {
                                $loket_petugas = 1;
                            }
                        }
                        else
                        {
                            $loket_petugas = 1;
                        }
                    }
                    else
                        {
                            //langsung masukkan loket sesuai kode tujuan
                            $loket_petugas = $data->kunjungan_tujuan;
                        }

                    $data->kunjungan_petugas_uid = Auth::user()->user_uid;
                    $data->kunjungan_jam_datang = \Carbon\Carbon::now();
                    $data->kunjungan_loket_petugas = $loket_petugas;
                    $data->kunjungan_flag_antrian = 'dalam_layanan';
                    $data->update();
                    $arr = array(
                        'status' => true,
                        'message' => 'Data kunjungan an. ' . $data->Pengunjung->pengunjung_nama . ' berhasil mulai dilayani',
                        'data' => true
                        );
                }
            }
            else
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Kunjungan ini sudah dilayani'
                );
            }
        }
        return Response()->json($arr);
    }
    public function AkhirLayanan(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data tidak tersedia'
        );
        $data = Kunjungan::where([['kunjungan_uid', $request->uid], ['kunjungan_jam_pulang', NULL]])->first();
        if ($data) {
            $data->kunjungan_jam_pulang = Carbon::now();
            $data->kunjungan_flag_antrian = 'selesai';
            $data->kunjungan_flag_feedback = 'belum';
            $data->update();
            //kirim email untuk isi feedback
            //pre email
            //kirim mail
            if ($data->kunjungan_tujuan == 1)
            {
                $layanan = $data->Tujuan->tujuan_nama .' - '. $data->LayananKantor->layanan_kantor_nama;
            }
            elseif ($data->kunjungan_tujuan == 2)
            {
                $layanan = $data->Tujuan->tujuan_nama .' - '. $data->LayananPst->layanan_pst_nama;
            }
            else
            {
                $layanan = $data->Tujuan->tujuan_nama;
            }
            $body = new \stdClass();
            $body->kunjungan_uid = $data->kunjungan_uid;
            $body->pengunjung_nama = $data->Pengunjung->pengunjung_nama;
            $body->pengunjung_email = $data->Pengunjung->pengunjung_email;
            $body->pengunjung_nomor_hp = $data->Pengunjung->pengunjung_nomor_hp;
            $body->kunjungan_tanggal = Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y');
            $body->layanan = $layanan;
            $body->link_feedback = route('kunjungan.feedback',$data->kunjungan_uid);
            $body->petugas = $data->Petugas->name;
            $body->nama_aplikasi = $this->nama_aplikasi;
            $body->nama_satker = $this->nama_satker;
            $body->alamat_satker = $this->alamat_satker;

            if (filter_var($data->Pengunjung->pengunjung_email, FILTER_VALIDATE_EMAIL))
            {
                if (ENV('APP_KIRIM_MAIL') == true) {
                    Mail::to($data->Pengunjung->pengunjung_email)->send(new KirimFeedback($body));
                }
                //batas
            }

            $arr = array(
                'status' => true,
                'message' => 'Data kunjungan an. ' . $data->Pengunjung->pengunjung_nama . ' berhasil diakhiri'
            );
            //cek dulu wa nya bisa apa ngga
            //kirim wa baru
            //persiapan untuk WA
            $recipients = $data->Pengunjung->pengunjung_nomor_hp;
            $recipients = $this->cek_nomor_hp($recipients);
            $message = '#Hai *'.$data->Pengunjung->pengunjung_nama.'*'.chr(10).chr(10)
                .'Kami ingin mengucapkan terima kasih atas kunjungan Anda ke '.$this->nama_satker.' pada hari *'.Carbon::parse($data->kunjungan_tanggal)->isoFormat('dddd, D MMMM Y').'*. Kami berharap Anda memiliki pengalaman yang menyenangkan bersama kami.'.chr(10)
                .'#Detil Kunjungan'.chr(10)
                .'UID : *'.$body->kunjungan_uid.'*'.chr(10)
                .'Nama : *'.$body->pengunjung_nama.'*'.chr(10)
                .'Email : *'.$body->pengunjung_email.'*'.chr(10)
                .'Nomor HP : *'.$body->pengunjung_nomor_hp.'*'.chr(10)
                .'Petugas yang melayani : *'.$body->petugas.'*'.chr(10)
                .'Untuk meningkatkan layanan, kami sangat menghargai jika Anda dapat meluangkan beberapa menit untuk mengisi feedback ini. Tanggapan Anda sangat berharga bagi kami untuk terus memberikan pelayanan terbaik. Link feedback ada dibagian bawah pesan ini.'.chr(10).chr(10)
                .route('kunjungan.feedback',$data->kunjungan_uid).chr(10).chr(10)
                .$this->nama_aplikasi.chr(10)
                .$this->nama_satker.chr(10)
                .$this->alamat_satker;
             //input ke log pesan
            $new_wa = new Whatsapp();
            $new_wa->wa_tanggal = Carbon::today()->format('Y-m-d');
            $new_wa->wa_uid = Generate::Kode(8);
            $new_wa->wa_pengunjung_uid = $data->pengunjung_uid;
            $new_wa->wa_kunjungan_uid = $data->kunjungan_uid;
            $new_wa->wa_target = $recipients;
            $new_wa->wa_message = $message;
            $new_wa->save();
            //cek dulu wa nya bisa apa ngga
            if (ENV('APP_WA_LOKAL_MODE') == true) {
                try {
                    $result = $this->whatsappService->sendMessage($recipients, $message);
                    //return response()->json($result);
                    if ($result)
                    {
                        $new_wa->wa_message_id = $result['results']['message_id'];
                        $new_wa->wa_status = $result['results']['status'];
                        $new_wa->wa_flag = 2;
                        $new_wa->update();
                    }
                    //$arr = $result;
                } catch (\Throwable $e) {
                    $error = Log::error('WA LOKAL: ' . $e->getMessage());
                    //return response()->json(['error' => 'Internal Server Error'],500);
                    $new_wa->wa_status = $error ;
                    $new_wa->wa_flag = 3;
                    $new_wa->update();
                }
            }
            //batas
        }
        return Response()->json($arr);
    }
    public function TindakLanjutSave(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data tindak lanjut tidak di simpan'
        );
        if (Auth::user())
        {
            $data = Kunjungan::where('kunjungan_uid',$request->kunjungan_uid)->first();
            if ($data)
            {
                $data->kunjungan_tindak_lanjut = $request->kunjungan_tindak_lanjut;
                $data->update();

                $arr = array(
                    'status'=>true,
                    'message'=>'Tindak lanjut untuk kunjungan an. '.$data->Pengunjung->pengunjung_nama.' sudah tersimpan',
                    'data'=>true
                );
            }
        }
        return Response()->json($arr);
    }
    public function PetugasSimpan(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data tidak tersedia'
        );
        if (Auth::user())
        {
            $data = Kunjungan::where('kunjungan_uid',$request->kunjungan_uid)->first();
            if ($data)
            {
                $data->kunjungan_petugas_uid = $request->petugas_uid;
                $data->update();

                $arr = array(
                    'status'=>true,
                    'message'=>'Data Petugas Layanan kunjungan an. '.$data->Pengunjung->pengunjung_nama.' tanggal '.$data->kunjungan_tanggal.' telah berhasil di update'
                );
            }
            else
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Data kunjungan tidak tersedia'
                );
            }
        }
        else
        {
            $arr = array(
                'status'=>false,
                'message'=>'Anda tidak mempunyai hak akses'
            );
        }
        return Response()->json($arr);
    }
    public function FlagAntrianUpdate(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data tidak tersedia'
        );
        if (Auth::user())
        {
            $data = Kunjungan::where('kunjungan_uid',$request->kunjungan_uid)->first();
            if ($data)
            {
                $data->kunjungan_flag_antrian = $request->kunjungan_flag_antrian;
                if ($request->kunjungan_flag_antrian == 'ruang_tunggu')
                {
                    $jam_datang = null;
                    $jam_pulang = null;
                    $petugas_uid = null;
                    $loket_petugas = 0;
                }
                elseif ($request->kunjungan_flag_antrian == 'dalam_layanan')
                {
                    $jam_datang = Carbon::parse($data->tanggal . ' 08:00:00')->format('Y-m-d H:i:s');
                    $jam_pulang = null;
                    $petugas_uid = Auth::user()->user_uid;
                    $loket_petugas = 1;
                }
                else
                {
                    $jam_datang = Carbon::parse($data->tanggal . ' 08:00:00')->format('Y-m-d H:i:s');
                    $jam_pulang = Carbon::parse($data->tanggal . ' 10:00:00')->format('Y-m-d H:i:s');
                    $petugas_uid = Auth::user()->user_uid;
                    $loket_petugas = 1;
                }
                $data->kunjungan_petugas_uid = $petugas_uid;
                $data->kunjungan_jam_datang = $jam_datang;
                $data->kunjungan_jam_pulang = $jam_pulang;
                $data->kunjungan_loket_petugas = $loket_petugas;
                $data->update();

                $arr = array(
                    'status'=>true,
                    'message'=>'Data kunjungan an. '.$data->Pengunjung->pengunjung_nama.' tanggal '.$data->kunjungan_tanggal.' telah berhasil di update'
                );
            }
            else
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Data kunjungan tidak tersedia'
                );
            }
        }
        else
        {
            $arr = array(
                'status'=>false,
                'message'=>'Anda tidak mempunyai hak akses'
            );
        }
        return Response()->json($arr);
    }
    public function JenisKunjunganSave(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data tidak di simpan'
        );
        if (Auth::user())
        {
            $data = Kunjungan::where('kunjungan_uid',$request->kunjungan_uid)->first();
            if ($data)
            {
                if ($request->kunjungan_jenis == 'perorangan')
                {
                    //perorangan
                    if ($data->Pengunjung->pengunjung_jenis_kelamin == 'laki_laki')
                    {
                        $jumlah_orang = 1;
                        $jumlah_pria = 1;
                        $jumlah_wanita = 0;
                    }
                    else
                    {
                        $jumlah_orang = 1;
                        $jumlah_pria = 0;
                        $jumlah_wanita = 1;
                    }
                }
                else
                {
                    $jumlah_orang = $request->jumlah_orang;
                    $jumlah_pria = $request->jumlah_pria;
                    $jumlah_wanita = $request->jumlah_wanita;
                }
                $data->kunjungan_jenis = $request->kunjungan_jenis;
                $data->kunjungan_jumlah_orang = $jumlah_orang;
                $data->kunjungan_jumlah_pria = $jumlah_pria;
                $data->kunjungan_jumlah_wanita = $jumlah_wanita;
                $data->update();

                $arr = array(
                    'status'=>true,
                    'message'=>'Jenis kunjungan an. '.$data->Pengunjung->pengunjung_nama.' sudah diperbarui',
                    'data'=>true
                );
            }
        }
        return Response()->json($arr);
    }
    public function TujuanBaruSave(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data tidak di simpan'
        );
        if (Auth::user())
        {
            $data = Kunjungan::where('kunjungan_uid',$request->kunjungan_uid)->first();
            if ($data)
            {
                if ($request->kunjungan_tujuan_baru == 1)
                {
                    $layanan_kantor_baru = $request->kunjungan_layanan_kantor_baru;
                    $layanan_pst_baru = 99;
                }
                elseif ($request->kunjungan_tujuan_baru == 2)
                {
                    $layanan_pst_baru = $request->kunjungan_layanan_pst_baru;
                    $layanan_kantor_baru = 99;
                }
                else
                {
                    $layanan_pst_baru = 99;
                    $layanan_kantor_baru = 99;
                }
                $data->kunjungan_tujuan = $request->kunjungan_tujuan_baru;
                $data->kunjungan_layanan_kantor = $layanan_kantor_baru;
                $data->kunjungan_layanan_pst = $layanan_pst_baru;
                $data->update();

                $arr = array(
                    'status'=>true,
                    'message'=>'Tujuan untuk kunjungan an. '.$data->Pengunjung->pengunjung_nama.' sudah diperbarui',
                    'data'=>true
                );
            }
        }
        return Response()->json($arr);
    }
    public function KirimLinkFeedback(Request $request)
    {
        $data = Kunjungan::where('kunjungan_uid', $request->uid)->first();
        $arr = array(
            'status' => false,
            'message' => 'Kunjungan tidak ditemukan'
        );
        if ($data) {
            //kirim mail
            if ($data->kunjungan_tujuan == 1)
            {
                $layanan = $data->Tujuan->tujuan_nama .' - '. $data->LayananKantor->layanan_kantor_nama;
            }
            elseif ($data->kunjungan_tujuan == 2)
            {
                $layanan = $data->Tujuan->tujuan_nama .' - '. $data->LayananPst->layanan_pst_nama;
            }
            else
            {
                $layanan = $data->Tujuan->tujuan_nama;
            }
            //kirim mail
            $body = new \stdClass();
            $body->kunjungan_uid = $data->kunjungan_uid;
            $body->pengunjung_nama = $data->Pengunjung->pengunjung_nama;
            $body->pengunjung_email = $data->Pengunjung->pengunjung_email;
            $body->pengunjung_nomor_hp = $data->Pengunjung->pengunjung_nomor_hp;
            $body->kunjungan_tanggal = Carbon::parse($data->created_at)->isoFormat('dddd, D MMMM Y');
            $body->layanan = $layanan;
            $body->link_feedback = route('kunjungan.feedback',$data->kunjungan_uid);
            $body->petugas = $data->Petugas->name;
            $body->nama_aplikasi = $this->nama_aplikasi;
            $body->nama_satker = $this->nama_satker;
            $body->alamat_satker = $this->alamat_satker;
            if (filter_var($data->Pengunjung->pengunjung_email, FILTER_VALIDATE_EMAIL))
            {
                if (ENV('APP_KIRIM_MAIL') == true) {
                    Mail::to($data->Pengunjung->pengunjung_email)->send(new KirimFeedback($body));

                    $arr = array(
                        'status' => true,
                        'message' => 'Link feedback kunjungan an. '.$data->Pengunjung->pengunjung_nama.' sudah dikirim ke alamat email '.$data->Pengunjung->pengunjung_email
                    );
                }
                else
                {
                    $arr = array(
                        'status' => false,
                        'message' => 'APP_KIRIM_MAIL bernilai False di .env'
                    );
                }
                //batas
            }
            else
            {
                $arr = array(
                    'status' => false,
                    'message' => 'Alamat email Kunjungan an. '.$data->Pengunjung->pengunjung_nama.' tidak sesuai format'
                );
            }

            //cek dulu wa nya bisa apa ngga
            //kirim wa baru
            //persiapan untuk WA
            if (ENV('APP_WA_LOKAL_MODE') == true) {
                //cek dulu wa nya bisa apa ngga
                //kirim wa baru
                //persiapan untuk WA .'*'.$this->link_skd.'*'.chr(10)
                //https://btamu-kabkota.test/k/f/CQFWC7R
                //$link_fb = $this->$this->link_feedback.$data->kunjungan_uid;
                //$link_feedback = 'https://btamu-kabkota.test/k/f/CQFWC7R';
                $recipients = $data->Pengunjung->pengunjung_nomor_hp;
                $recipients = $this->cek_nomor_hp($recipients);
                $message = '#Hai *'.$data->Pengunjung->pengunjung_nama.'*'.chr(10).chr(10)
                .'Kami ingin mengucapkan terima kasih atas kunjungan Anda ke '.$this->nama_satker.' pada hari *'.Carbon::parse($data->kunjungan_tanggal)->isoFormat('dddd, D MMMM Y').'*. Kami berharap Anda memiliki pengalaman yang menyenangkan bersama kami.'.chr(10)
                .'#Detil Kunjungan'.chr(10)
                .'UID : *'.$body->kunjungan_uid.'*'.chr(10)
                .'Nama : *'.$body->pengunjung_nama.'*'.chr(10)
                .'Email : *'.$body->pengunjung_email.'*'.chr(10)
                .'Nomor HP : *'.$body->pengunjung_nomor_hp.'*'.chr(10)
                .'Petugas yang melayani : *'.$body->petugas.'*'.chr(10)
                .'Untuk meningkatkan layanan, kami sangat menghargai jika Anda dapat meluangkan beberapa menit untuk mengisi feedback ini. Tanggapan Anda sangat berharga bagi kami untuk terus memberikan pelayanan terbaik. Link feedback ada dibagian bawah pesan ini.'.chr(10).chr(10)
                .route('kunjungan.feedback',$data->kunjungan_uid).chr(10).chr(10)
                .$this->nama_aplikasi.chr(10)
                .$this->nama_satker.chr(10)
                .$this->alamat_satker;
                //simpan ke tabael m_whatsapp
                $new_wa = new Whatsapp();
                $new_wa->wa_tanggal = Carbon::today()->format('Y-m-d');
                $new_wa->wa_uid = Generate::Kode(8);
                $new_wa->wa_pengunjung_uid = $data->pengunjung_uid;
                $new_wa->wa_kunjungan_uid = $body->kunjungan_uid;
                $new_wa->wa_target = $recipients;
                $new_wa->wa_message = $message;
                $new_wa->save();

                try {
                    $result = $this->whatsappService->sendMessage($recipients,$message);
                    if ($result)
                    {
                        $new_wa->wa_message_id = $result['results']['message_id'];
                        $new_wa->wa_status = $result['results']['status'];
                        $new_wa->wa_flag = 'terkirim';
                        $new_wa->update();
                    }
                } catch (\Throwable $e) {
                    $error = Log::error('WA LOKAL: ' . $e->getMessage());
                    //return response()->json(['error' => 'Internal Server Error'],500);
                    $new_wa->wa_status = $error ;
                    $new_wa->wa_flag = 'gagal';
                    $new_wa->update();
                }
            }
            //batas
            //batas kirim wa */
        }

        #dd($request->all());
        return Response()->json($arr);
    }
    public function TambahPermintaan()
    {
        $Pendidikan = Pendidikan::orderBy('pendidikan_kode', 'asc')->get();
        $Tujuan = Tujuan::where('tujuan_kode','>','2')->orderBy('tujuan_kode', 'asc')->get();
        $LayananPst = LayananPst::where('layanan_pst_kode','<','99')->orderBy('layanan_pst_kode', 'asc')->get();
        $LayananKantor = LayananKantor::orderBy('layanan_kantor_kode', 'asc')->get();
        return view('kunjungan.permintaan',[
            'Pendidikan' => $Pendidikan,
            'LayananPst'=>$LayananPst,
            'LayananKantor'=>$LayananKantor,
            'Tujuan'=>$Tujuan
            ]);
    }
    public function simpanPermintaan(Request $request)
    {
        //dd($request->all());
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
        $cek_kunjungan = Kunjungan::where([['pengunjung_uid', $data->pengunjung_uid], ['kunjungan_tanggal', Carbon::parse($request->kunjungan_tanggal)->format('Y-m-d')], ['kunjungan_tujuan', $request->kunjungan_tujuan]])->count();
        if ($cek_kunjungan > 0) {
            //sudah ada kasih info kalo sudah mengisi
            $pesan_error = 'Data pengunjung ' . $data->pengunjung_nama . ' sudah pernah mengisi permintaan hari tanggal ' . Carbon::parse($request->kunjungan_tanggal)->isoFormat('dddd, D MMMM Y');
            $warna_error = 'danger';
        }
        else
        {
            //masukkan ke tabel kunjungan
            //cek dulu antrian ada sesuai layanan
            //kalo pst cek layanan pst juga
            $data_antrian = Kunjungan::where([['kunjungan_tanggal', Carbon::parse($request->kunjungan_tanggal)->format('Y-m-d')], ['kunjungan_tujuan',$request->kunjungan_tujuan]])->orderBy('kunjungan_nomor_antrian', 'desc')->first();
            $data_layanan_utama = Tujuan::where('tujuan_kode',$request->kunjungan_tujuan)->first();
            $nomor_antrian_inisial = $data_layanan_utama->tujuan_inisial;
            if ($data_antrian) {
                //kalo sudah ada antrian
                $nomor_selanjutnya = $data_antrian->kunjungan_nomor_antrian + 1;
            }
            else {
                //belum ada sama sekali
                $nomor_selanjutnya = 1;
            }
            $jumlah_tamu = 1;
            //cek jenis kelamin ambil dari query data diatas
            if ($data->pengunjung_jenis_kelamin == 'laki_laki') {
                $laki = 1;
                $wanita = 0;
            } else {
                $laki = 0;
                $wanita = 1;
            }
            //flag antrian langsung aja diubah
            $jam_datang = Carbon::parse($request->kunjungan_tanggal . ' 08:00:00')->format('Y-m-d H:i:s');
            $jam_pulang = Carbon::parse($request->kunjungan_tanggal . ' 15:30:00')->format('Y-m-d H:i:s');
            if (Auth::user())
            {
                $petugas_uid = Auth::user()->user_uid;
            }
            else
            {
                $petugas_uid = null;
            }
            $loket_petugas = 1;
            $newdata = new Kunjungan();
            $newdata->pengunjung_uid = $data->pengunjung_uid;
            $newdata->kunjungan_uid = Generate::Kode(7);
            $newdata->kunjungan_tanggal = Carbon::parse($request->kunjungan_tanggal)->format('Y-m-d');
            $newdata->kunjungan_keperluan = $request->kunjungan_keperluan;
            $newdata->kunjungan_jenis = 'perorangan'; //perorangan
            $newdata->kunjungan_tujuan = $request->kunjungan_tujuan;
            if ($namafile_kunjungan != NULL) {
                $newdata->kunjungan_foto = $namafile_kunjungan;
            }
            $newdata->kunjungan_jumlah_orang = $jumlah_tamu;
            $newdata->kunjungan_jumlah_pria = $laki;
            $newdata->kunjungan_jumlah_wanita = $wanita;
            $newdata->kunjungan_nomor_antrian = $nomor_selanjutnya;
            $newdata->kunjungan_teks_antrian = $nomor_antrian_inisial . '-' . sprintf("%03d", $nomor_selanjutnya);
            $newdata->kunjungan_petugas_uid = $petugas_uid;
            $newdata->kunjungan_jam_datang = $jam_datang;
            $newdata->kunjungan_jam_pulang = $jam_pulang;
            $newdata->kunjungan_loket_petugas = $loket_petugas;
            $newdata->kunjungan_flag_antrian = 'selesai';
            $newdata->save();
            //tambah total kunjungan di tabel pengunjung

            $total_kunjungan = $data->pengunjung_total_kunjungan;
            $data->pengunjung_total_kunjungan = $total_kunjungan + 1;
            $data->update();
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
            $body->kunjungan_tanggal = \Carbon\Carbon::parse($newdata->kunjungan_tanggal)->isoFormat('dddd, D MMMM Y');
            $body->layanan = $layanan;
            $body->nomor_antrian = $newdata->kunjungan_teks_antrian;
            $body->nama_aplikasi = $this->nama_aplikasi;
            $body->nama_satker = $this->nama_satker;
            $body->alamat_satker = $this->alamat_satker;
            //cek email valid apa tidak
            if (filter_var($newdata->Pengunjung->pengunjung_email, FILTER_VALIDATE_EMAIL))
            {
                if (ENV('APP_KIRIM_MAIL') == true) {
                    Mail::to($newdata->Pengunjung->pengunjung_email)->send(new KirimAntrian($body));
                }
                //batas
            }
            $header_error = "<strong>Terimakasih</strong>";
            $pesan_error = "Data kunjungan an. <strong><i>" . trim($request->pengunjung_nama) . "</i></strong> berhasil ditambahkan";
            $warna_error = "success";
        }
        Session::flash('message_header', $header_error);
        Session::flash('message', $pesan_error);
        Session::flash('message_type', $warna_error);
        return redirect()->route('depan');
    }
    public function laporan(Request $request)
    {
        if (request('tahun') == NULL) {
            $tahun_filter = Carbon::today()->format('Y');
        } else {
            $tahun_filter = request('tahun');
        }
        $data = \DB::table('m_bulan')
        ->leftJoin(\DB::Raw("(select month(kunjungan_tanggal) as bln_total, count(*) as jumlah_kunjungan, sum(kunjungan_jumlah_orang) as jumlah_total, sum(kunjungan_jumlah_pria) as jumlah_laki, sum(kunjungan_jumlah_wanita) as jumlah_wanita from m_kunjungan where year(kunjungan_tanggal)='".$tahun_filter."' GROUP by bln_total) as total"),'m_bulan.id','=','total.bln_total')
        ->select(\DB::Raw('bulan_nama,bulan_nama_pendek,COALESCE(jumlah_kunjungan,0) as jumlah_kunjungan, COALESCE(jumlah_total,0) as jumlah_total, COALESCE(jumlah_laki,0) as jumlah_laki, COALESCE(jumlah_wanita,0) as jumlah_wanita'))->get();
        //dd($data);
        return view('kunjungan.laporan',['data'=>$data,'tahun'=>$tahun_filter]);
    }
    public function HapusKunjungan(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data kunjungan tidak tersedia'
        );
        if (Auth::user())
        {
            $data = Kunjungan::where('kunjungan_uid',$request->uid)->first();
            if ($data)
            {
                $nama = $data->Pengunjung->pengunjung_nama;
                $tanggal = $data->kunjungan_tanggal;
                $file = $data->kunjungan_foto;
                //hapus dulu kunjungan
                $data->delete();
                //cek dulu file fotonya ada tidak
                if (Storage::disk('public')->exists($file))
                {
                    Storage::disk('public')->delete($file);
                }
                $arr = array(
                    'status' => true,
                    'message' => 'Data kunjungan an. ' . $nama . ' tanggal '.$tanggal.' berhasil dihapus',
                    'data' => true,
                );
            }
            else
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Data kunjungan tidak tersedia'
                );
            }
        }
        return Response()->json($arr);
    }
}
