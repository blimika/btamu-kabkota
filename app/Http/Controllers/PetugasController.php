<?php

namespace App\Http\Controllers;

use App\Akses;
use App\Exports\FormatPetugas;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\Generate;
use App\Imports\ImportPetugas;
use App\Kunjungan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Mail\KirimAntrian;
use App\Mail\KirimFeedback;
use Excel;
use App\Services\WhatsAppService;

class PetugasController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('login.index');
    }
    public function login(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'username'    => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. Simpan kredensial
        $credentials = $request->only('username', 'password');

        // 3. Coba lakukan autentikasi
        if (Auth::attempt($credentials)) {
            // Jika berhasil
            //update tabel akses
            $data = Akses::firstOrCreate(
                ['akses_ip' => $request->getClientIp()],
                ['akses_ip' => $request->getClientIp(), 'akses_flag' => '1']
            );
            $user = User::where('username',Auth::user()->username)->first();
            $user->user_last_login = Carbon::now()->toDateTimeString(); // Menggunakan helper now() untuk waktu saat ini
            $user->user_last_ip = $request->getClientIp(); // Mengambil IP dari request
            $user->update(); // 3. Simpan perubahan ke database
            $request->session()->regenerate(); // Regenerate session untuk keamanan
            return redirect()->intended('/'); // Redirect ke halaman yang dituju sebelumnya atau ke /dashboard
        }

        // 4. Jika gagal
        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->withInput(); // Kembali ke halaman login dengan pesan error
    }

    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
    public function index()
    {
        $data_users = User::get();
        return view('petugas.index',['dataPetugas'=>$data_users]);
    }
    public function simpan(Request $request)
    {
        $data = User::where('username',trim($request->username))->orWhere('email',trim($request->email))->orWhere('user_telepon',trim($request->telepon))->first();
        $arr = array(
            'status'=>false,
            'hasil'=>'Username ('.trim($request->username).'), E-Mail ('.trim($request->email).') atau Nomor HP ('.trim($request->telepon).') sudah digunakan'
        );
        if (!$data)
        {
            //$email_kodever = Str::random(10);
            //simpan data member
            $data = new User();
            $data->user_uid = Generate::Kode(6);
            $data->user_level = $request->level;
            $data->name = trim($request->name);
            $data->username = trim($request->username);
            $data->email = trim($request->email);
            $data->ganti_email = trim($request->email);
            $data->user_telepon = trim($request->telepon);
            $data->password = bcrypt($request->passwd);
            $data->email_kodever = Str::random(10);
            $data->user_flag = 'aktif';
            $data->save();
            $arr = array(
                'status'=>true,
                'hasil'=>'Data petugas an. '.$request->username.' berhasil ditambahkan'
            );
        }
        #dd($request->all());
        return Response()->json($arr);
    }
    public function PageListPetugas(Request $request)
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
        $totalRecords = User::select('count(*) as allcount')->count();
        $totalRecordswithFilter = User::select('count(*) as allcount')
            ->where('users.name', 'like', '%' .$searchValue . '%')
            ->orWhere('user_level','like','%' .$searchValue . '%')
            ->orWhere('username','like','%' .$searchValue . '%')
            ->count();

        // Fetch records
        $records = User::orderBy($columnName,$columnSortOrder)
            ->where('users.name', 'like', '%' .$searchValue . '%')
            ->orWhere('user_level','like','%' .$searchValue . '%')
            ->orWhere('username','like','%' .$searchValue . '%')
            ->select('users.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $record->id;
            $user_uid = $record->user_uid;
            $name = $record->name;
            $username = $record->username;
            $user_foto = $record->user_foto;
            $user_level = $record->user_level;
            $email = $record->email;
            $email_ganti = $record->email_ganti;
            $user_telepon = $record->user_telepon;
            $user_flag = $record->user_flag;
            $email_kodever = $record->email_kodever;
            $user_last_login = $record->user_last_login;
            $user_last_ip = $record->user_last_ip;

            if ($record->user_foto != NULL)
            {
                if (Storage::disk('public')->exists($record->user_foto))
                {
                    $user_foto = '<a class="image-popup" href="'.asset('storage'.$record->user_foto).'" title="Nama : '.$record->name.'">
                <img src="'.asset('storage'.$record->user_foto).'" class="img-circle" width="60" height="60" class="img-responsive" />
            </a>';
                }
                else
                {
                    $user_foto = '<a class="image-popup" href="https://placehold.co/480x360/0022FF/FFFFFF/?text=photo+tidak+ada" title="Nama : '.$record->name.'">
                    <img src="https://placehold.co/480x360/0022FF/FFFFFF/?text=photo+tidak+ada" alt="image"  class="img-circle" width="60" height="60" />
                    </a>';
                }
            }
            else
            {
                $user_foto = '<a class="image-popup" href="https://placehold.co/480x360/0022FF/FFFFFF/?text=photo+tidak+ada" title="Nama : '.$record->name.'">
                <img src="https://placehold.co/480x360/0022FF/FFFFFF/?text=photo+tidak+ada" alt="image"  class="img-circle" width="60" height="60" />
                </a>';
            }
            if ($record->flag == 0)
            {
                $link_aktivasi = '<a class="dropdown-item kirimaktivasi" href="#" data-id="'.$record->id.'" data-uid="'.$record->user_uid.'" data-nama="'.$record->name.'" data-flagmember="'.$record->user_flag.'">Kirim Aktivasi</a>';
            }
            else
            {
                $link_aktivasi='';
            }

            if (Auth::user()->user_level == 'admin')
            {
                //hanya admin yg keluar aksinya ini
                $aksi ='
                    <div class="btn-group">
                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti-settings"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" data-id="'.$record->id.'" data-uid="'.$record->user_uid.'" data-toggle="modal" data-target="#ViewPetugasModal">View</a>
                        <a class="dropdown-item" href="#" data-id="'.$record->id.'" data-uid="'.$record->user_uid.'" data-toggle="modal" data-target="#EditPetugasModal">Edit</a>
                        <a class="dropdown-item" href="#" data-id="'.$record->id.'" data-uid="'.$record->user_uid.'" data-nama="'.$record->name.'" data-username="'.$record->username.'" data-toggle="modal" data-target="#GantiPasswdModal">Ganti Password</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item ubahflagmember" href="#" data-id="'.$record->id.'" data-uid="'.$record->user_uid.'" data-nama="'.$record->name.'" data-flagmember="'.$record->user_flag.'">Ubah Flag</a>
                        '.$link_aktivasi.'
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item hapusmember" href="#" data-id="'.$record->id.'" data-nama="'.$record->name.'">Hapus</a>
                    </div>
                </div>
                ';
            }
            else
            {
                $aksi ="";
            }
            $data_arr[] = array(
                "id" => $id,
                "name"=>$name,
                "username"=> $username,
                "email"=>$email,
                "user_level"=>$user_level,
                "user_telepon"=>$user_telepon,
                "user_flag"=>$user_flag,
                "user_uid"=>$user_uid,
                "user_last_login"=>$user_last_login,
                "user_last_ip"=>$user_last_ip,
                "user_foto"=>$user_foto,
                "aksi"=>$aksi
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
    public function Penilaian()
    {
        $data_bulan = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        $data_bulan_pendek = array(
            1 => 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'
        );
        $data_tahun = DB::table('m_kunjungan')
            ->selectRaw('year(kunjungan_tanggal) as tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();
        if (request('tahun') == NULL) {
            $tahun_filter = date('Y');
        }
        elseif (request('tahun') == 0) {
            $tahun_filter = date('Y');
        }
        else {
            $tahun_filter = request('tahun');
        }
        $data = User::where('user_flag','aktif')->get();

        return view('petugas.penilaian',[
            'data'=>$data,
            'tahun'=>$tahun_filter,
            'data_tahun'=>$data_tahun,
            'data_bulan'=>$data_bulan_pendek,
        ]);
    }
    public function FormatPetugas()
    {
        $fileName = 'format-petugas-';
        $data = [
            [
                'username' => 'username',
                'name'=> 'nama lengkap',
                'email' => 'email : format xxx@gmail.com',
                'password' => 'password min 1 karakter',
                'user_level' => 'operator/admin, pilih salah satu',
                'user_telepon' => 'nomor whatsapp',
            ]
        ];
        $namafile = $fileName . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new FormatPetugas($data), $namafile);
    }
    public function ImportPetugas(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Import data tidak berhasil',
            'data'=>false,
        );

        if ($request->hasFile('file_import')) {
            $file = $request->file('file_import'); //GET FILE
            Excel::import(new ImportPetugas, $file); //IMPORT FILE
            $arr = array(
                'status'=>true,
                'message'=>'Import data berhasil',
                'data'=>true,
            );
        }
        return Response()->json($arr);
    }
    public function UpdatePetugasData(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Update petugas tidak berhasil',
        );
        $data = User::where('user_uid',$request->uid)->first();
        $cek_data = User::where('username',trim($request->username))->first();
        if ($data and (!$cek_data or $cek_data->user_uid == $data->user_uid))
        {
            $data->name = trim($request->name);
            $data->username = trim($request->username);
            $data->user_level = trim($request->level);
            $data->email = trim($request->email);
            $data->user_telepon = trim($request->telepon);
            $data->update();
            $arr = array(
                'status'=>true,
                'message'=>'Data petugas an. '.$request->name.' ('.$request->username.') berhasil diupdate'
            );
        }
        else
        {
            $arr = array(
                'status'=>false,
                'message'=>'Username '.$request->username.' sudah ada yang menggunakan',
            );
        }
        return Response()->json($arr);
    }
    public function AdmGantiPasswd(Request $request)
    {
        //hanya admin dan operator yg bisa ganti password nya
        //tapi tidak bisa ganti password sendiri harus dari menu profil
        //admin boleh ganti password level dibawahnya
        if (!Auth::User()) {
            $arr = array(
                'status'=>false,
                'message'=>'Anda tidak memiliki akses untuk ganti password petugas'
            );
            return Response()->json($arr);
        }
        $data = User::where('user_uid',$request->uid)->first();
        $arr = array(
            'status'=>false,
            'message'=>'Data petugas tidak ditemukan'
        );
        if ($data)
        {
            if ($data->username == 'admin')
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Superadmin tidak ganti password melalui menu ini, harus melalui menu profil'
                );
            }
            elseif (Auth::User()->username == $data->username)
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Tidak bisa mengganti password sendiri, harus dari menu profil'
                );
            }
            elseif (Auth::User()->user_level == "operator")
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Operator tidak bisa mengganti password operator/admin'
                );
            }
            elseif ($request->passwd_baru != $request->ulangi_passwd_baru )
            {
                /*
                id: gp_id,
                passwd_baru: gp_passwd,
                ulangi_passwd_baru: gp_ulangi_passwd,
                */
                $arr = array(
                    'status'=>false,
                    'message'=>'Password baru dan ulangi password tidak sama'
                );
            }
            else
            {
                //$data->password = bcrypt($request->passwd);
                $data->password = bcrypt($request->passwd_baru);
                $data->update();

                $arr = array(
                    'status'=>true,
                    'message'=>'Password petugas an. '.$data->name.' berhasil diganti'
                );
            }
        }

        return Response()->json($arr);
    }
    public function profil()
    {
        $data = Kunjungan::where('kunjungan_petugas_uid',Auth::user()->user_uid)->take(10)->get();
        return view('petugas.profil',[
            'data' => $data
        ]);
    }
    public function UpdateProfil(Request $request)
    {
        $cekData = User::where('username',trim($request->username))->orWhere('email',trim($request->email))->orWhere('user_telepon',trim($request->telepon))->first();
        $data = User::where('user_uid',Auth::user()->user_uid)->first();
        $arr = array(
            'status'=>false,
            'message'=>'Username ('.trim($request->username).'), E-Mail ('.trim($request->email).') atau Nomor HP ('.trim($request->telepon).') sudah digunakan/username tidak ditemukan'
        );
        if ($data && (!$cekData or ($cekData && $cekData->user_uid == Auth::user()->user_uid)))
        {
            //$email_kodever = Str::random(10);
            //simpan data member
            $data->name = trim($request->name);
            $data->username = trim($request->username);
            $data->email = trim($request->email);
            $data->user_telepon = trim($request->telepon);
            $data->update();
            $arr = array(
                'status'=>true,
                'message'=>'Data member an. '.$request->name.' ('.$request->username.') berhasil diupdate, jika mengganti username silakan login ulang dgn username baru'
            );
        }
        //dd($request->all());
        return Response()->json($arr);
    }
    public function GantiPassword(Request $request)
    {
        $arr = array(
            'status'=>false,
            'hasil'=>'Data profil tidak ditemukan'
        );
        $data = User::where('user_uid',Auth::user()->user_uid)->first();
        if ($data)
        {
            /*
             passwd_lama: passwd_lama,
                passwd_baru: passwd_baru,
                ulangi_passwd_baru: ulangi_passwd_baru,
                */
            if (!\Hash::check($request->passwd_lama, $data->password))
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Password lama tidak sama'
                );
            }
            elseif ($request->passwd_baru != $request->ulangi_passwd_baru)
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Password baru tidak sama dengan ulangi password baru'
                );
            }
            else
            {
                $data->password = bcrypt($request->passwd_baru);
                $data->update();
                $arr = array(
                    'status'=>true,
                    'message'=>'Password berhasil diganti, anda akan otomatis logout, dan masuk dengan password baru'
                );
            }
        }
        return Response()->json($arr);
    }
}
