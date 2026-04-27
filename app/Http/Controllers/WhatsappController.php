<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use App\Tanggal;
use App\Whatsapp;
use Carbon\Carbon;
use App\Helpers\Generate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
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
        $this->nama_aplikasi = ENV('NAMA_APLIKASI');
        $this->link_skd = get_setting('APP_LINK_SKD');
        $this->nama_satker = get_setting('NAMA_SATKER');
        $this->alamat_satker = get_setting('ALAMAT_SATKER');
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
    public function index()
    {
        $data = Whatsapp::orderBy('created_at','desc')->take(100)->get();
        return view('whatsapp.index',[
                    'data'=>$data
                ]);
    }
    public function NotifJaga(Request $request)
    {
        $arr = array(
            'status' => false,
            'message' => 'WhatsApp Api Gateway Error'
        );
        $data = Tanggal::where('tanggal_angka',Carbon::today()->format('Y-m-d'))->first();
        if ($data)
        {
            if ($data->tanggal_jenis == 'kerja')
            {
                if ($data->tanggal_petugas1_uid != null && $data->tanggal_petugas2_uid)
                {
                    $hp_petugas1 = $data->Petugas1->user_telepon;
                    $hp_petugas2 = $data->Petugas2->user_telepon;
                    $hp_petugas3 = $data->Petugas3->user_telepon;

                    $message1 = "#Hai *".$data->Petugas1->name."*\n\n"
                    . "Selamat pagi,\n"
                    . "Pengingat tugas jaga Layanan hari ini,\n"
                    . "*".\Carbon\Carbon::parse($data->tanggal_angka)->isoFormat('dddd, D MMMM Y')."*\n\n"
                    . "Terimakasih dan selamat bertugas\n\n"
                    . $this->nama_aplikasi."\n"
                    . $this->nama_satker."\n"
                    . $this->alamat_satker;
                    $message2 = "#Hai *".$data->Petugas2->name."*\n\n"
                    . "Selamat pagi,\n"
                    . "Pengingat tugas jaga Layanan hari ini,\n"
                    . "*".\Carbon\Carbon::parse($data->tanggal_angka)->isoFormat('dddd, D MMMM Y')."*\n\n"
                    . "Terimakasih dan selamat bertugas\n\n"
                    . $this->nama_aplikasi."\n"
                    . $this->nama_satker."\n"
                    . $this->alamat_satker;
                    $message3 = "#Hai *".$data->Petugas3->name."*\n\n"
                    . "Selamat pagi,\n"
                    . "Pengingat tugas jaga Layanan hari ini,\n"
                    . "*".\Carbon\Carbon::parse($data->tanggal_angka)->isoFormat('dddd, D MMMM Y')."*\n\n"
                    . "Terimakasih dan selamat bertugas\n\n"
                    . $this->nama_aplikasi."\n"
                    . $this->nama_satker."\n"
                    . $this->alamat_satker;
                    //simpan log dulu
                     //input ke log pesan
                    $new_wa1 = new Whatsapp();
                    $new_wa1->wa_tanggal = Carbon::today()->format('Y-m-d');
                    $new_wa1->wa_uid = Generate::Kode(8);
                    $new_wa1->wa_target = $hp_petugas1;
                    $new_wa1->wa_message = $message1;
                    $new_wa1->save();

                    $new_wa2 = new Whatsapp();
                    $new_wa2->wa_tanggal = Carbon::today()->format('Y-m-d');
                    $new_wa2->wa_uid = Generate::Kode(8);
                    $new_wa2->wa_target = $hp_petugas2;
                    $new_wa2->wa_message = $message2;
                    $new_wa2->save();

                    $new_wa3 = new Whatsapp();
                    $new_wa3->wa_tanggal = Carbon::today()->format('Y-m-d');
                    $new_wa3->wa_uid = Generate::Kode(8);
                    $new_wa3->wa_target = $hp_petugas3;
                    $new_wa3->wa_message = $message3;
                    $new_wa3->save();

                    if (ENV('APP_WA_LOKAL_MODE') == true && $hp_petugas1 != null) {
                        try {
                            $response1 = $this->whatsappService->sendMessage($hp_petugas1, $message1);
                            // Karena service mengembalikan response()->json(), kita ubah menjadi array
                            // agar mudah dibaca oleh PHP
                            $result1 = $response1->getData(true);

                            if ($result1['status'] === true) {
                                // Jika berhasil dikirim
                                // Key 'data' berasal dari format JSON service yang baru kita buat
                                $new_wa1->wa_message_id = $result1['data']['results']['message_id'] ?? null;
                                $new_wa1->wa_status = $result1['data']['results']['status'] ?? 'Sent successfully';
                                $new_wa1->wa_flag = 'terkirim'; // Flag sukses
                            } else {
                                // Jika nomor tidak ada WA atau ditolak service
                                $new_wa1->wa_status = $result1['message']; // Berisi pesan "nomor tidak terdaftar..."
                                $new_wa1->wa_flag = 'gagal'; // Flag gagal
                            }

                            // Gunakan save() lagi untuk menyimpan perubahan status
                            $new_wa1->update();

                        } catch (\Throwable $e) {
                            Log::error('WA LOKAL [Notif Petugas 1]: ' . $e->getMessage());

                            // PERBAIKAN: Tangkap pesan aslinya, BUKAN hasil dari fungsi Log::error
                            // Agar jika gagal, Anda tahu alasannya saat melihat tabel database
                            $new_wa1->wa_status = 'Error System: ' . $e->getMessage();
                            $new_wa1->wa_flag = 'gagal'; // Flag gagal
                            $new_wa1->update();
                        }
                    }
                    sleep(1);
                    //petugas 2
                    if (ENV('APP_WA_LOKAL_MODE') == true && $hp_petugas2 != null) {
                        try {
                            $response2 = $this->whatsappService->sendMessage($hp_petugas2, $message2);
                            // Karena service mengembalikan response()->json(), kita ubah menjadi array
                            // agar mudah dibaca oleh PHP
                            $result2 = $response2->getData(true);

                            if ($result2['status'] === true) {
                                // Jika berhasil dikirim
                                // Key 'data' berasal dari format JSON service yang baru kita buat
                                $new_wa2->wa_message_id = $result2['data']['results']['message_id'] ?? null;
                                $new_wa2->wa_status = $result2['data']['results']['status'] ?? 'Sent successfully';
                                $new_wa2->wa_flag = 'terkirim'; // Flag sukses
                            } else {
                                // Jika nomor tidak ada WA atau ditolak service
                                $new_wa2->wa_status = $result2['message']; // Berisi pesan "nomor tidak terdaftar..."
                                $new_wa2->wa_flag = 'gagal'; // Flag gagal
                            }

                            // Gunakan save() lagi untuk menyimpan perubahan status
                            $new_wa2->update();

                        } catch (\Throwable $e) {
                            Log::error('WA LOKAL [Notif Petugas 2]: ' . $e->getMessage());

                            // PERBAIKAN: Tangkap pesan aslinya, BUKAN hasil dari fungsi Log::error
                            // Agar jika gagal, Anda tahu alasannya saat melihat tabel database
                            $new_wa2->wa_status = 'Error System: ' . $e->getMessage();
                            $new_wa2->wa_flag = 'gagal'; // Flag gagal
                            $new_wa2->update();
                        }
                    }
                    sleep(1);
                    //petugas 3
                    if (ENV('APP_WA_LOKAL_MODE') == true && $hp_petugas3 != null) {
                        try {
                            $response3 = $this->whatsappService->sendMessage($hp_petugas3, $message3);
                            // Karena service mengembalikan response()->json(), kita ubah menjadi array
                            // agar mudah dibaca oleh PHP
                            $result3 = $response3->getData(true);

                            if ($result3['status'] === true) {
                                // Jika berhasil dikirim
                                // Key 'data' berasal dari format JSON service yang baru kita buat
                                $new_wa3->wa_message_id = $result3['data']['results']['message_id'] ?? null;
                                $new_wa3->wa_status = $result3['data']['results']['status'] ?? 'Sent successfully';
                                $new_wa3->wa_flag = 'terkirim'; // Flag sukses
                            } else {
                                // Jika nomor tidak ada WA atau ditolak service
                                $new_wa3->wa_status = $result3['message']; // Berisi pesan "nomor tidak terdaftar..."
                                $new_wa3->wa_flag = 'gagal'; // Flag gagal
                            }

                            // Gunakan save() lagi untuk menyimpan perubahan status
                            $new_wa3->update();

                        } catch (\Throwable $e) {
                            Log::error('WA LOKAL [Notif Petugas 3]: ' . $e->getMessage());

                            // PERBAIKAN: Tangkap pesan aslinya, BUKAN hasil dari fungsi Log::error
                            // Agar jika gagal, Anda tahu alasannya saat melihat tabel database
                            $new_wa3->wa_status = 'Error System: ' . $e->getMessage();
                            $new_wa3->wa_flag = 'gagal'; // Flag gagal
                            $new_wa3->update();
                        }
                    }
                    $arr = array(
                        'status' => true,
                        'message' => "Notifikasi sudah dikirimkan ke petugas jaga"
                    );
                }
                else
                {
                    $arr = array(
                        'status' => false,
                        'message' => "Data petugas jaga masih kosong, belum ada jadwal"
                    );
                }
            }
            else
            {
                $arr = array(
                        'status' => false,
                        'message' => "Hari libur : ".$data->tanggal_deskripsi
                    );
            }

        }
        else
        {
            $arr = array(
                'status' => false,
                'message' => "Data petugas belum tersedia"
            );
        }
        return Response()->json($arr);
    }
}
