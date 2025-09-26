<?php

namespace App\Http\Controllers;

use App\Pengunjung;
use Illuminate\Http\Request;
use App\Pendidikan;
use App\Kunjungan;
use App\Tujuan;
use App\LayananPst;
use App\LayananKantor;
use App\Tanggal;
use App\Whatsapp;
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

class PengunjungController extends Controller
{
    protected $WhatsappService;
    protected $cek_nomor_hp;
    protected $link_skd;
    protected $nama_aplikasi;
    protected $nama_satker;
    protected $alamat_satker;

    public function __construct()
    {
        $this->link_skd = env('APP_LINK_SKD');
        $this->nama_aplikasi = ENV('NAMA_APLIKASI');
        $this->nama_satker = ENV('NAMA_SATKER');
        $this->alamat_satker = ENV('ALAMAT_SATKER');
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
    public function Timeline($uid)
    {
        $data = Pengunjung::with('Pendidikan','Kunjungan','Kunjungan.Tujuan','Kunjungan.LayananKantor','Kunjungan.LayananPst')->where('pengunjung_uid',$uid)->first();
        return view('pengunjung.timeline',['data'=>$data]);
    }
    public function KirimLinkSKD(Request $request)
    {
        //ambil data pengunjung

        $data = Pengunjung::where('pengunjung_uid', $request->pengunjung_uid)->first();
        $arr = array(
            'status' => false,
            'message' => 'Data Pengunjung tidak ditemukan'
        );
        if ($data) {
            $body = new \stdClass();
            $body->pengunjung_nama = $data->pengunjung_nama;
            $body->pengunjung_email = $data->pengunjung_email;
            $body->link_skd = $this->link_skd;
            $body->nama_aplikasi = $this->nama_aplikasi;
            $body->nama_satker = $this->nama_satker;
            $body->alamat_satker = $this->alamat_satker;
            if (filter_var($data->pengunjung_email, FILTER_VALIDATE_EMAIL))
            {
                if (ENV('APP_KIRIM_MAIL') == true) {
                    Mail::to($data->pengunjung_email)->send(new KirimLinkSKD($body));

                    $arr = array(
                        'status' => true,
                        'message' => 'Link SKD sudah dikirim ke alamat email '.$data->pengunjung_nama.' ('.$data->pengunjung_email.')'
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
                    'hasil' => 'Alamat email an. '.$data->pengunjung_nama.' tidak sesuai format'
                );
            }

            //cek dulu wa nya bisa apa ngga
            //kirim wa baru
            //persiapan untuk WA
            $recipients = $data->pengunjung_nomor_hp;
            $recipients = $this->cek_nomor_hp($recipients);
            $message = '#Hai *'.$data->pengunjung_nama.'*'.chr(10).chr(10).
            'Kami mengucapkan terima kasih atas kunjungan Anda ke BPS Provinsi Nusa Tenggara Barat. Dalam rangka meningkatkan kualitas data dan pelayanan, BPS Provinsi NTB menyelenggarakan Survei Kebutuhan Data (SKD).'.chr(10).chr(10)
            .'Bapak/Ibu terpilih menjadi responden kami.'.chr(10)
            .'Mohon kesediaannya untuk mengisi dengan lengkap pertanyaan-pertanyaan pada link dibawah ini. Survei ini hanya membutuhkan waktu beberapa menit untuk diisi.'.chr(10)
            .'Jika mengalami kendala dalam klik link, silakan copy paste link dibawah ini'.chr(10)
            .'*'.$this->link_skd.'*'.chr(10)
            .'Kerahasiaan jawaban Anda dilindungi Undang-undang No.16 Tahun 1997 tentang Statistik.'.chr(10)
            .'*Terima kasih atas partisipasi Anda!*'.chr(10).chr(10)
            .'Sekali lagi, terima kasih atas kunjungan Anda dan kami berharap dapat menyambut Anda kembali di masa depan.'.chr(10)
            .'Hubungi kami di:'.chr(10)
            .'▶ Email : *pst5200@bps.go.id*'.chr(10)
            .'🗣 Chat dgn Customer Service: *https://wa.me/6281999952002*'.chr(10).chr(10)
            .'Salam hangat,'.chr(10)
            .'BPS Provinsi Nusa Tenggara Barat'.chr(10)
            .'Jl. Dr. Soedjono No. 74 Mataram NTB 83116';
            //simpan ke tabael m_whatsapp
            /*
            $new_wa = new Whatsapp();
            $new_wa->wa_tanggal = Carbon::today()->format('Y-m-d');
            $new_wa->wa_uid = Generate::Kode(8);
            $new_wa->wa_pengunjung_uid = $data->pengunjung_uid;
            $new_wa->wa_kunjungan_uid = $request->kunjungan_uid;
            $new_wa->wa_target = $recipients;
            $new_wa->wa_message = $message;
            $new_wa->save();
            //cek dulu wa nya bisa apa ngga
            if (ENV('APP_WA_LOKAL_MODE') == true) {
                try {
                    $result = $this->WAservice->sendMessage($recipients, $message);
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
            //batas kirim wa */
        }

        #dd($request->all());
        return Response()->json($arr);
    }
}
