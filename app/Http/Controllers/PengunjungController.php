<?php

namespace App\Http\Controllers;

use App\Exports\FormatPengunjung;
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
use App\Services\WhatsAppService;

class PengunjungController extends Controller
{
    protected $whatsappService;
    protected $cek_nomor_hp;
    protected $link_skd;
    protected $nama_aplikasi;
    protected $nama_satker;
    protected $alamat_satker;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->link_skd = env('APP_LINK_SKD');
        $this->nama_aplikasi = ENV('NAMA_APLIKASI');
        $this->nama_satker = ENV('NAMA_SATKER');
        $this->alamat_satker = ENV('ALAMAT_SATKER');
        $this->whatsAppService = $whatsAppService;
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
                    'message' => 'Alamat email an. '.$data->pengunjung_nama.' tidak sesuai format'
                );
            }

            if (ENV('APP_WA_LOKAL_MODE') == true) {
                //cek dulu wa nya bisa apa ngga
                //kirim wa baru
                //persiapan untuk WA .'*'.$this->link_skd.'*'.chr(10)
                $recipients = $data->pengunjung_nomor_hp;
                $recipients = $this->cek_nomor_hp($recipients);
                $message = '#Hai *'.$data->pengunjung_nama.'*'.chr(10).chr(10).
                'Kami mengucapkan terima kasih atas kunjungan Anda ke '.$this->nama_satker.'. Dalam rangka meningkatkan kualitas data dan pelayanan, kami menyelenggarakan Survei Kebutuhan Data (SKD).'.chr(10).chr(10)
                .'Bapak/Ibu terpilih menjadi responden kami. Mohon kesediaannya untuk mengisi dengan lengkap pertanyaan-pertanyaan pada link dibawah ini. Survei ini hanya membutuhkan waktu beberapa menit untuk diisi.'.chr(10)
                .'Kerahasiaan jawaban Anda dilindungi Undang-undang No.16 Tahun 1997 tentang Statistik.'.chr(10)
                .'*Terima kasih atas partisipasi Anda!*'.chr(10).chr(10)
                .'Salam hangat,'.chr(10)
                .$this->nama_satker.chr(10);
                //simpan ke tabael m_whatsapp
                $new_wa = new Whatsapp();
                $new_wa->wa_tanggal = Carbon::today()->format('Y-m-d');
                $new_wa->wa_uid = Generate::Kode(8);
                $new_wa->wa_pengunjung_uid = $data->pengunjung_uid;
                $new_wa->wa_kunjungan_uid = $request->kunjungan_uid;
                $new_wa->wa_target = $recipients;
                $new_wa->wa_message = $message;
                $new_wa->save();

                try {
                    $result = $this->whatsAppService->sendLink($recipients, $this->link_skd, $message);
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
    public function index()
    {
        $Pendidikan = Pendidikan::orderBy('id', 'asc')->get();
        return view('pengunjung.index',[
            'MasterPendidikan' => $Pendidikan
        ]);
    }
    public function PageList(Request $request)
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
        $totalRecords = Pengunjung::count();
        //total record searching
        $totalRecordswithFilter =  DB::table('m_pengunjung')
            ->leftJoin('m_pendidikan', 'm_pengunjung.pengunjung_pendidikan', '=', 'm_pendidikan.pendidikan_kode')
            ->when($searchValue, function ($q) use ($searchValue) {
                return $q->where('m_pengunjung.pengunjung_nama', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.pengunjung_uid', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.pengunjung_nomor_hp', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.pengunjung_pekerjaan', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pendidikan.pendidikan_nama', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.created_at', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.pengunjung_email', 'like', '%' . $searchValue . '%');
            })->count();
        // Fetch records
        $records = DB::table('m_pengunjung')
            ->leftJoin('m_pendidikan', 'm_pengunjung.pengunjung_pendidikan', '=', 'm_pendidikan.pendidikan_kode')
            ->when($searchValue, function ($q) use ($searchValue) {
                return $q->where('m_pengunjung.pengunjung_nama', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.pengunjung_uid', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.pengunjung_nomor_hp', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.pengunjung_pekerjaan', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pendidikan.pendidikan_nama', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.created_at', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_pengunjung.pengunjung_email', 'like', '%' . $searchValue . '%');
            })
            ->select('m_pengunjung.*','m_pendidikan.pendidikan_nama as nama_pendidikan')
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName, $columnSortOrder)
            ->get();

            $data_arr = array();

            foreach ($records as $item) {

                $aksi = '
                <div class="btn-group">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ti-settings"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" data-uid="' . $item->pengunjung_uid . '" data-toggle="modal" data-target="#ViewPengunjungModal">View</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-uid="' . $item->pengunjung_uid . '" data-toggle="modal" data-target="#EditPengunjungModal">Edit</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item hapuspengunjung" href="#" data-id="' . $item->pengunjung_id . '" data-uid="' . $item->pengunjung_uid . '" data-nama="' . $item->pengunjung_nama . '">Delete</a>

                </div>
            </div>
            ';
             //jenis kelamin
                if ($item->pengunjung_jenis_kelamin == 'laki_laki') {
                    $jk = '<span class="badge badge-info badge-pill">L</span>';
                }
                else {
                    $jk = '<span class="badge badge-danger badge-pill">P</span>';
                }
                //pendidikan
                if ($item->pengunjung_pendidikan == 1)
                {
                    $pendidikan = '<span class="badge badge-danger badge-pill">' . $item->nama_pendidikan . '</span>';
                }
                elseif ($item->pengunjung_pendidikan == 2)
                {
                    $pendidikan = '<span class="badge badge-info badge-pill">' . $item->nama_pendidikan . '</span>';
                }
                elseif ($item->pengunjung_pendidikan == 3)
                {
                    $pendidikan = '<span class="badge badge-success badge-pill">' . $item->nama_pendidikan . '</span>';
                }
                elseif ($item->pengunjung_pendidikan == 4)
                {
                    $pendidikan = '<span class="badge badge-warning badge-pill">' . $item->nama_pendidikan . '</span>';
                }
                else
                {
                    $pendidikan = '<span class="badge badge-primary badge-pill">' . $item->nama_pendidikan . '</span>';
                }
                $link_nama = '<a href="#" data-uid="' . $item->pengunjung_uid . '" data-toggle="modal" data-target="#ViewPengunjungModal">'.$item->pengunjung_nama.'</a>';
                $data_arr[] = array(
                    "pengunjung_uid" => $item->pengunjung_uid,
                    "pengunjung_nama" => $link_nama .'<br />'.$jk,
                    "pengunjung_nomor_hp" => $item->pengunjung_nomor_hp,
                    "pengunjung_tahun_lahir" => $item->pengunjung_tahun_lahir .'<br />('.(Carbon::now()->format('Y')-$item->pengunjung_tahun_lahir).' tahun)',
                    "pengunjung_pekerjaan" => $item->pengunjung_pekerjaan,
                    "pengunjung_pendidikan" => $pendidikan,
                    "pengunjung_email" => $item->pengunjung_email,
                    "pengunjung_total_kunjungan" => $item->pengunjung_total_kunjungan,
                    "created_at"=>$item->created_at,
                    "aksi" => $aksi
                );
            }

            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                "aaData" => $data_arr
            );

            echo json_encode($response);
            exit;
    }
    public function hapus(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data pengunjung tidak tersedia'
        );
        $data = Pengunjung::where('pengunjung_uid',$request->uid)->first();
        if ($data)
        {
            //hapus semua foto2
            if ($data->pengunjung_foto_profil != "")
            {
                if (Storage::disk('public')->exists($data->pengunjung_foto_profil))
                {
                    Storage::disk('public')->delete($data->pengunjung_foto_profil);
                }
            }
            //hapus user yang terhubung
            if ($data->pengunjung_user_uid != "")
            {
                $data_user = User::where('user_uid',$data->pengunjung_user_uid)->first();
                if ($data_user->user_foto != "")
                {
                    if (Storage::disk('public')->exists($data_user->user_foto))
                    {
                        Storage::disk('public')->delete($data_user->user_foto);
                    }
                }
                $data_user->delete();
            }
            //hapus data pengunjung
            $data->delete();
            ///cari kunjungan dah hapus foto2
            $data_visit = Kunjungan::where('pengunjung_uid',$request->uid)->get();
            if ($data_visit)
            {
                foreach ($data_visit as $item) {
                    if ($item->kunjungan_foto != "")
                    {
                        if (Storage::disk('public')->exists($item->kunjungan_foto))
                        {
                            Storage::disk('public')->delete($item->kunjungan_foto);
                        }
                    }
                }
                Kunjungan::where('pengunjung_uid',$request->uid)->delete();

                $arr = array(
                    'status'=>true,
                    'message'=>'Data pengunjung an '. $request->nama .' beserta data kunjungan berhasil dihapus',
                    'data'=>true
                );
            }
        }
        return Response()->json($arr);
    }
    public function update(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Data pengunjung tidak tersedia'
        );
        if (Auth::user())
        {
            $data = Pengunjung::where('pengunjung_uid',$request->pengunjung_uid)->first();
            if ($data)
            {
                $data->pengunjung_nomor_hp = $request->pengunjung_nomor_hp;
                $data->pengunjung_nama = $request->pengunjung_nama;
                $data->pengunjung_jenis_kelamin = $request->pengunjung_jk;
                $data->pengunjung_tahun_lahir = $request->pengunjung_tahun_lahir;
                $data->pengunjung_pekerjaan = $request->pengunjung_pekerjaan;
                $data->pengunjung_pendidikan = $request->pengunjung_pendidikan;
                $data->pengunjung_email = $request->pengunjung_email;
                $data->pengunjung_alamat = $request->pengunjung_alamat;
                $data->update();
                $arr = array(
                    'status'=>true,
                    'message'=>'Data pengunjung berhasil di perbarui'
                );
            }
        }
        else
        {
            $arr = array(
                'status'=>false,
                'message'=>'anda tidak mempunyai akses untuk update pengunjung'
            );
        }
        return Response()->json($arr);
    }
    public function feedback()
    {
        if (request('tahun') == NULL) {
            $tahun_filter = 0;
        } else {
            $tahun_filter = request('tahun');
        }
        $data_tahun = DB::table('m_kunjungan')
            ->selectRaw('year(kunjungan_tanggal) as tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();
        $data = Kunjungan::when($tahun_filter > 0, function ($query) use ($tahun_filter) {
                    return $query->whereYear('kunjungan_tanggal', $tahun_filter);
                })
                ->where('kunjungan_flag_feedback','sudah')
                ->orderBy('kunjungan_tanggal', 'desc')
                ->get();
        return view('pengunjung.feedback',[
            'data'=>$data,
            'data_tahun'=>$data_tahun,
            'tahun'=>$tahun_filter
        ]);
    }
    public function PageListFeedback(Request $request)
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
            ->leftJoin('m_layanan_pst', 'm_kunjungan.kunjungan_layanan_pst', '=', 'm_layanan_pst.layanan_pst_kode')
            ->when($searchValue, function ($q) use ($searchValue) {
                return $q->where('m_pengunjung.pengunjung_nama', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_kunjungan.kunjungan_uid', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_kunjungan.kunjungan_tanggal', 'like', '%' . $searchValue . '%')
                         ->orWhere('users.name', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_kunjungan.kunjungan_komentar_feedback', 'like', '%' . $searchValue . '%');
            })
            ->where('m_kunjungan.kunjungan_flag_feedback','sudah')->count();

        // Fetch records
        $records = DB::table('m_kunjungan')
            ->leftJoin('m_pengunjung', 'm_kunjungan.pengunjung_uid', '=', 'm_pengunjung.pengunjung_uid')
            ->leftJoin('m_tujuan', 'm_kunjungan.kunjungan_tujuan', '=', 'm_tujuan.tujuan_kode')
            ->leftJoin('users', 'm_kunjungan.kunjungan_petugas_uid', '=', 'users.user_uid')
            ->leftJoin('m_layanan_pst', 'm_kunjungan.kunjungan_layanan_pst', '=', 'm_layanan_pst.layanan_pst_kode')
            ->when($searchValue, function ($q) use ($searchValue) {
                return $q->where('m_pengunjung.pengunjung_nama', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_kunjungan.kunjungan_uid', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_kunjungan.kunjungan_tanggal', 'like', '%' . $searchValue . '%')
                         ->orWhere('users.name', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_kunjungan.kunjungan_komentar_feedback', 'like', '%' . $searchValue . '%');
            })
            ->where('m_kunjungan.kunjungan_flag_feedback','sudah')
            ->select('m_kunjungan.*', 'm_pengunjung.pengunjung_nama','m_pengunjung.pengunjung_email', 'm_pengunjung.pengunjung_jenis_kelamin', 'm_tujuan.tujuan_inisial as tujuan_inisial', 'm_tujuan.tujuan_nama as tujuan_nama', 'users.name', 'users.username', 'm_layanan_pst.layanan_pst_nama as kunjungan_pst_teks')
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName, $columnSortOrder)
            ->get();

        //inisiasi aawal
        $data_arr = array();

        //list data
        foreach ($records as $item) {

            $aksi = '<a href="'. route('timeline',$item->pengunjung_uid).'" class="btn btn-sm btn-info waves-effect" target="_blank">TIMELINE</a>';
            if ($item->kunjungan_tujuan == 2)
            {
                //ke pst ambil layanan pst
                $tujuan = $item->kunjungan_pst_teks;

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
            //edit tampilang warna2
            //warna layanan utama
            $layanan_utama = '<span class="badge '.$warna_layanan_utama.' badge-pill">'.$tujuan.'</span>';
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
                $petugas = $item->name .'<br />'. $loket_petugas;
            }
            else {
                $petugas = '<span class="badge badge-danger badge-pill">belum ada</span';
            }
            //jenis kelamin
            if ($item->pengunjung_jenis_kelamin == 'laki_laki') {
                $jk = '<span class="badge badge-info badge-pill">L</span>';
            }
            else {
                $jk = '<span class="badge badge-danger badge-pill">P</span>';
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
            $nilai_petugas='';
            for ($i = 1; $i < 7; $i++)
            {
                if ($i <= $item->kunjungan_nilai_feedback)
                {
                    $nilai_petugas .= '<span class="fa fa-star text-warning"></span>';
                }
                else
                {
                    $nilai_petugas .= ' <span class="fa fa-star"></span>';
                }
            }
            $nilai_sarpras='';
            for ($i = 1; $i < 7; $i++)
            {
                if ($i <= $item->kunjungan_sarpras_feedback)
                {
                    $nilai_sarpras .= '<span class="fa fa-star text-warning"></span>';
                }
                else
                {
                    $nilai_sarpras .= ' <span class="fa fa-star"></span>';
                }
            }
            if ($item->kunjungan_tujuan == 2)
            {
                $layanan_utama = $tujuan .' '.$layanan_utama;
            }
            $komentar_feedback = '<i>'.$item->kunjungan_komentar_feedback .'</i>';
            //batas
            $data_arr[] = array(
                "kunjungan_uid" => $item->kunjungan_uid,
                "kunjungan_tanggal" => $item->kunjungan_tanggal,
                "pengunjung_nama" => $item->pengunjung_nama .'<br />'.$jk,
                "kunjungan_tujuan" => $layanan_utama,
                "kunjungan_nilai_feedback" => $nilai_petugas,
                "kunjungan_sarpras_feedback" => $nilai_sarpras,
                "kunjungan_komentar_feedback"=>$komentar_feedback,
                "kunjungan_petugas_id" => $petugas,
                "aksi" => $aksi
            );
        }
        //batas list

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }
    public function Format()
    {
        $fileName = 'format-pengunjung-';
        $data = [
            [
                'nomor_hp' => 'nomor  hp format 08xxxxx',
                'nama_lengkap'=> 'nama lengkap',
                'jenis_kelamin' => 'laki_laki/perempuan',
                'tahun_lahir' => 'tahun lahir, 4 digit',
                'email' => 'format email@gmail.com',
                'pendidikan' => '1 <== SMA, 2 Diploma, 3 Sarjana, 4 Magister, 5 Doktor',
                'pekerjaan' => 'isikan detil pekerjaannya',
                'alamat' => 'alamat pengunjung',
            ]
        ];
        $namafile = $fileName . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new FormatPengunjung($data), $namafile);
    }
}
