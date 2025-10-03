<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;
use App\Helpers\Generate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Kunjungan;
use App\Tujuan;
use App\LayananPst;
use App\LayananKantor;
use App\Tanggal;
use App\Whatsapp;
use App\Services\WhatsAppService;

class InfoPetugas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $whatsappService;
    protected $cek_nomor_hp;
    protected $link_skd;
    protected $nama_aplikasi;
    protected $nama_satker;
    protected $alamat_satker;
    protected $link_feedback;

    protected $signature = 'info:petugas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifikasi ke WA Petugas Jaga';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
        $this->link_skd = env('APP_LINK_SKD');
        $this->nama_aplikasi = ENV('NAMA_APLIKASI');
        $this->nama_satker = ENV('NAMA_SATKER');
        $this->alamat_satker = ENV('ALAMAT_SATKER');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
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
    public function handle()
    {
       //ambil jadwal dulu
        $data = Tanggal::where('tanggal_angka',Carbon::today()->format('Y-m-d'))->first();
        if ($data)
        {
            if ($data->tanggal_jenis == 'kerja')
            {
                if ($data->tanggal_petugas1_uid != null && $data->tanggal_petugas2_uid != null)
                {
                    //$data1 = User::where('id',$data->petugas1_id)->first();
                    $hp_petugas1 = $data->Petugas1->user_telepon;
                    $hp_petugas2 = $data->Petugas2->user_telepon;
                    //dd($hp_petugas1);
                    $recipients1 = $this->cek_nomor_hp($hp_petugas1);
                    $recipients2 = $this->cek_nomor_hp($hp_petugas2);
                    $message1 = '#Hai *'.$data->Petugas1->name.'*'.chr(10).chr(10)
                    .'Selamat pagi,'.chr(10)
                    .'Pengingat tugas jaga Layanan hari ini,'.chr(10)
                    .'*'.\Carbon\Carbon::parse($data->tanggal_angka)->isoFormat('dddd, D MMMM Y').'*'.chr(10).chr(10)
                    .'Terimakasih dan selamat bertugas'.chr(10).chr(10)
                    .$this->nama_aplikasi.chr(10)
                    .$this->nama_satker;
                    $message2 = '#Hai *'.$data->Petugas2->name.'*'.chr(10).chr(10)
                    .'Selamat pagi,'.chr(10)
                    .'Pengingat tugas jaga Layanan hari ini,'.chr(10)
                    .'*'.\Carbon\Carbon::parse($data->tanggal_angka)->isoFormat('dddd, D MMMM Y').'*'.chr(10).chr(10)
                    .'Terimakasih dan selamat bertugas'.chr(10).chr(10)
                    .$this->nama_aplikasi.chr(10)
                    .$this->nama_satker;
                    //simpan log dulu
                     //input ke log pesan
                    $new_wa1 = new Whatsapp();
                    $new_wa1->wa_tanggal = Carbon::today()->format('Y-m-d');
                    $new_wa1->wa_uid = Generate::Kode(8);
                    $new_wa1->wa_target = $recipients1;
                    $new_wa1->wa_message = $message1;
                    $new_wa1->save();

                    $new_wa2 = new Whatsapp();
                    $new_wa2->wa_tanggal = Carbon::today()->format('Y-m-d');
                    $new_wa2->wa_uid = Generate::Kode(8);
                    $new_wa2->wa_target = $recipients2;
                    $new_wa2->wa_message = $message2;
                    $new_wa2->save();

                    if (ENV('APP_WA_LOKAL_MODE') == true) {
                        try {
                            $result1 = $this->whatsappService->sendMessage($recipients1, $message1);
                            if ($result1)
                            {
                                $new_wa1->wa_message_id = $result1['results']['message_id'];
                                $new_wa1->wa_status = $result1['results']['status'];
                                $new_wa1->wa_flag = 'terkirim';
                                $new_wa1->update();
                            }

                        } catch (\Throwable $e) {
                            $error1 = Log::error('WA LOKAL 1: ' . $e->getMessage());
                            //return response()->json(['error' => 'Internal Server Error'],500);
                            $new_wa1->wa_status = $error1 ;
                            $new_wa1->wa_flag = 'gagal';
                            $new_wa1->update();
                        }
                    }
                    sleep(1);
                    if (ENV('APP_WA_LOKAL_MODE') == true) {
                        try {
                            $result2 = $this->whatsappService->sendMessage($recipients2, $message2);
                            if ($result2)
                            {
                                $new_wa2->wa_message_id = $result2['results']['message_id'];
                                $new_wa2->wa_status = $result2['results']['status'];
                                $new_wa2->wa_flag = 'terkirim';
                                $new_wa2->update();
                            }

                        } catch (\Throwable $e) {
                            $error2 = Log::error('WA LOKAL 2: ' . $e->getMessage());
                            //return response()->json(['error' => 'Internal Server Error'],500);
                            $new_wa2->wa_status = $error2 ;
                            $new_wa2->wa_flag = 'gagal';
                            $new_wa2->update();
                        }
                    }

                    $error = "Notifikasi sudah dikirimkan ke petugas jaga";
                }
                else
                {
                     $error = "Data petugas jaga masih kosong, belum ada jadwal";
                }
            }
            else
            {
                $error = "Hari libur : ".$data->tanggal_deskripsi;
            }

        }
        else
        {
            $error = "Data petugas belum tersedia";
        }
        $this->info($error);
    }
}
