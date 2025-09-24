<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
                        <a class="dropdown-item" href="#" data-id="'.$record->id.'" data-uid="'.$record->user_uid.'" data-toggle="modal" data-target="#ViewMemberModal">View</a>
                        <a class="dropdown-item" href="#" data-id="'.$record->id.'" data-uid="'.$record->user_uid.'" data-toggle="modal" data-target="#EditMemberModal">Edit</a>
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
}
