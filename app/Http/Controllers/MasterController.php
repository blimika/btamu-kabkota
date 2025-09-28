<?php

namespace App\Http\Controllers;
use App\Helpers\Tanggal;
use App\Tanggal as AppTanggal;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\FormatJadwal;
use App\Imports\ImportJadwalPetugas;
use App\Tujuan;
use Excel;

class MasterController extends Controller
{
    public function tanggal()
    {
        $data_tahun = DB::table('m_tanggal')
            ->selectRaw('year(tanggal_angka) as tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();
        if (request('tahun') == NULL) {
            $tahun_filter = 0;
        } else {
            $tahun_filter = request('tahun');
        }
        $dataPetugas = User::get();
        return view('master.tanggal',[
            'dataPetugas'=>$dataPetugas,
            'data_tahun'=>$data_tahun,
            'tahun'=>$tahun_filter,
        ]);
    }
    public function FormatJadwal()
    {
        $fileName = 'format-jadwal-';
        $data = [
            [
                //'tahun_matrik' => null,
                'tanggal' => 'Format : YYYY-MM-DD',
                'petugas1_uid' => 'hanya kode uid 6 karakter',
                'petugas2_uid' => 'hanya kode uid 6 karakter',
            ]
        ];
        $namafile = $fileName . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new FormatJadwal($data), $namafile);
    }
    public function PageListTanggal(Request $request)
    {
        if (request('tahun') == NULL) {
            $tahun_filter = 0;
        }
        else {
            $tahun_filter = request('tahun');
        }
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
        $totalRecords = AppTanggal::count();
        $totalRecordswithFilter = DB::table('m_tanggal')
        ->leftJoin(DB::Raw("(select user_uid as uid_petugas1, name as nama_petugas1, username as username_petugas1 from users) as petugas1"),'m_tanggal.tanggal_petugas1_uid','=','petugas1.uid_petugas1')
        ->leftJoin(DB::Raw("(select user_uid as uid_petugas2, name as nama_petugas2, username as username_petugas2 from users) as petugas2"),'m_tanggal.tanggal_petugas2_uid','=','petugas2.uid_petugas2')
        ->when($searchValue, function ($q) use ($searchValue) {
            return $q->where('m_tanggal.tanggal_angka', 'like', '%' .$searchValue . '%')
                         ->orWhere('nama_petugas1', 'like', '%' . $searchValue . '%')
                         ->orWhere('nama_petugas2', 'like', '%' . $searchValue . '%')
                         ->orWhere('username_petugas1', 'like', '%' . $searchValue . '%')
                         ->orWhere('username_petugas2', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_tanggal.tanggal_deskripsi', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_tanggal.tanggal_hari', 'like', '%' . $searchValue . '%');
        })
        ->when($tahun_filter > 0, function ($query) use ($tahun_filter) {
            return $query->whereYear('m_tanggal.tanggal_angka', $tahun_filter);
        })
        ->count();

        // Fetch records
        $records = DB::table('m_tanggal')
             ->leftJoin(DB::Raw("(select user_uid as uid_petugas1, name as nama_petugas1, username as username_petugas1 from users) as petugas1"),'m_tanggal.tanggal_petugas1_uid','=','petugas1.uid_petugas1')
            ->leftJoin(DB::Raw("(select user_uid as uid_petugas2, name as nama_petugas2, username as username_petugas2 from users) as petugas2"),'m_tanggal.tanggal_petugas2_uid','=','petugas2.uid_petugas2')
            ->when($searchValue, function ($q) use ($searchValue) {
                return $q->where('m_tanggal.tanggal_angka', 'like', '%' .$searchValue . '%')
                            ->orWhere('nama_petugas1', 'like', '%' . $searchValue . '%')
                            ->orWhere('nama_petugas2', 'like', '%' . $searchValue . '%')
                            ->orWhere('username_petugas1', 'like', '%' . $searchValue . '%')
                            ->orWhere('username_petugas2', 'like', '%' . $searchValue . '%')
                            ->orWhere('m_tanggal.tanggal_deskripsi', 'like', '%' . $searchValue . '%')
                            ->orWhere('m_tanggal.tanggal_hari', 'like', '%' . $searchValue . '%');
            })
            ->when($tahun_filter > 0, function ($query) use ($tahun_filter) {
                return $query->whereYear('m_tanggal.tanggal_angka', $tahun_filter);
            })
            ->select('m_tanggal.*','petugas1.*','petugas2.*')
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName,$columnSortOrder)
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $record->id;
            $tanggal = $record->tanggal_angka;
            $hari = $record->tanggal_hari;
            $jenis = $record->tanggal_jenis;
            $deskripsi = $record->tanggal_deskripsi;
            $petugas1 = $record->nama_petugas1;
            $petugas2 = $record->nama_petugas2;
            if (Auth::user()->user_level == 'admin')
                {
                    if ($record->tanggal_jenis == 'kerja')
                    {
                        $link_edit_jadwal = '<a class="dropdown-item" href="#" data-id="'.$record->id.'" data-tanggal="'.$record->tanggal_angka.'" data-toggle="modal" data-target="#EditJadwal">Edit Jadwal</a>
                        <div class="dropdown-divider"></div>';
                    }
                    else
                    {
                        $link_edit_jadwal = '';
                    }
                    $aksi ='
                    <div class="btn-group">
                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti-settings"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" data-id="'.$record->id.'" data-tanggal="'.$record->tanggal_angka.'" data-toggle="modal" data-target="#EditTanggal">Edit Tanggal</a>
                        <div class="dropdown-divider"></div>
                        '.$link_edit_jadwal.'

                    </div>
                    </div>
                    ';
                }
                else
                {
                    $aksi ='';
                }
            $data_arr[] = array(
                "id" => $id,
                "tanggal_angka"=>$tanggal,
                "tanggal_hari"=>$hari,
                "tanggal_jenis"=> $jenis,
                "tanggal_deskripsi"=>$deskripsi,
                "tanggal_petugas1"=>$petugas1,
                "tanggal_petugas2"=>$petugas2,
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
    public function HapusAkses()
    {

    }
    public function GenerateTanggal(Request $request)
    {
        $nama_hari_panjang = array (0=> "Minggu", 1=> "Senin", 2=> "Selasa", 3=> "Rabu", 4=> "Kamis", 5=> "Jumat", 6=> "Sabtu");
        $nama_hari_pendek = array (0=> "Mgg", 1=> "Sen", 2=> "Sel", 3=> "Rab", 4=> "Kam", 5=> "Jum", 6=> "Sab");
        $r = file_get_contents(env('APP_API_TANGGAL'));
        $hari_libur = json_decode($r, true);
        $arr = array(
            'status'=>false,
            'hasil'=>'Data tanggal tahun '.$request->gentahun.' sudah pernah digenerate',
            'pesan_error'=>'Data tanggal tahun '.$request->gentahun.' sudah pernah digenerate',
        );
        $data = AppTanggal::whereYear('tanggal_angka',$request->gentahun)->count();
        if (!$data)
        {
            for ($b=1;$b<=12;$b++)
            {
                $tgl_cek = $request->gentahun.'-'.$b.'-01';
                $jumlah_hari = Carbon::parse($tgl_cek)->daysInMonth;
                for ($i=1;$i<=$jumlah_hari;$i++)
                {
                    $tgl_i = $request->gentahun.'-'.$b.'-'.$i;
                    //cek dulu apakah hari libur
                    $cek_libur = isset($hari_libur[Carbon::parse($tgl_i)->format("Y-m-d")])?true:false;
                    if ($cek_libur == true and $hari_libur[Carbon::parse($tgl_i)->format("Y-m-d")]['holiday'] == true)
                    {
                        //kode 1 = kerja, 2 = Sabtu/Minggu, 3 = libur
                        $j_libur = 'libur';
                        $deskripsi = $hari_libur[Carbon::parse($tgl_i)->format("Y-m-d")]['summary'][0];
                    }
                    else
                    {
                        //selain hari libur
                        //cek dulu hari sabtu apa minggu
                        if (Carbon::parse($tgl_i)->format('w') > 0 and Carbon::parse($tgl_i)->format('w') < 6)
                        {
                            $j_libur = 'kerja';
                            $deskripsi = '';
                        }
                        else {
                            //hari sabtu ato minggu
                            $j_libur = 'sabtu_minggu';
                            if (Carbon::parse($tgl_i)->format('w')==6)
                            {
                                $deskripsi = "Sabtu";
                            }
                            else
                            {
                                $deskripsi = "Minggu";
                            }
                        }
                    }

                    //save ke database
                    $data = new AppTanggal();
                    $data->tanggal_angka = Carbon::parse($tgl_i)->format("Y-m-d");
                    $data->tanggal_hari = $nama_hari_panjang[Carbon::parse($tgl_i)->format('w')];
                    $data->tanggal_jenis = $j_libur;
                    $data->tanggal_deskripsi = $deskripsi;
                    $data->save();
                }
            }
            $arr = array(
                'status'=>true,
                'hasil'=>'Data tanggal tahun '.$request->gentahun.' berhasil digenerate',
                'pesan_error'=>'Data tanggal tahun '.$request->gentahun.' berhasil digenerate',
            );
        }
        return Response()->json($arr);
    }
    public function UpdateTanggal(Request $request)
    {
        $data = AppTanggal::where('id',$request->id)->first();
        $arr = array(
            'status'=>false,
            'hasil'=>'Tanggal tidak ditemukan'
        );
        if ($data)
        {
            /*
             id: id,
                    jtgl: jtgl,
                    deskripsi: deskripsi,
                    hari_num: hari_num,
                    */
            if ($request->jtgl == 'kerja')
            {
                $deskripsi = "";
            }
            else
            {
                $deskripsi = trim($request->deskripsi);
            }
            $data->tanggal_jenis = $request->jtgl;
            $data->tanggal_deskripsi = $deskripsi;
            $data->update();
            $arr = array(
                'status'=>true,
                'hasil'=>'Tanggal sudah di update'
            );
        }
        return Response()->json($arr);
    }
    public function UpdateJadwal(Request $request)
    {
        $data = AppTanggal::where('id',$request->id)->first();
        $arr = array(
            'status'=>false,
            'hasil'=>'Jadwal tidak ditemukan'.$request->petugas1_uid.' / '.$request->petugas2_uid
        );
        if ($data)
        {
            /*
                id: id,
                petugas1_uid: petugas_1,
                petugas2_uid: petugas_2,
            */
            if ($request->petugas1_uid == $request->petugas2_uid)
            {
                $arr = array(
                    'status'=>false,
                    'hasil'=>'Petugas 1 dan Petugas 2 tidak boleh sama'
                );
            }
            else
            {
                $data1 = User::where('user_uid',$request->petugas1_uid)->first();
                $data2 = User::where('user_uid',$request->petugas2_uid)->first();
                //update data
                $data->tanggal_petugas1_uid = $request->petugas1_uid;
                //$data->petugas1_username = $data1->username;
                $data->tanggal_petugas2_uid = $request->petugas2_uid;
                //$data->petugas2_username = $data2->username;
                $data->update();
                $arr = array(
                    'status'=>true,
                    'hasil'=>'Jadwal petugas sudah diupdate'
                );
            }
        }
        return Response()->json($arr);
    }
    public function ImportJadwalPetugas(Request $request)
    {
        $arr = array(
            'status'=>false,
            'message'=>'Import jadwal petugas tidak berhasil',
            'data'=>'Import jadwal petugas tidak berhasil',
        );

        if ($request->hasFile('file_import')) {
            $file = $request->file('file_import'); //GET FILE
            Excel::import(new ImportJadwalPetugas, $file); //IMPORT FILE
            $arr = array(
                'status'=>true,
                'hasil'=>'Import jadwal petugas berhasil',
                'pesan_error'=>'Import jadwal petugas berhasil',
            );
        }
        return Response()->json($arr);
    }
    public function tujuan()
    {
        $tujuan = Tujuan::orderBy('tujuan_kode','asc')->get();
        return view('master.tujuan',['tujuan'=>$tujuan]);
    }
    public function PageListTujuan(Request $request)
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
        $totalRecords = AppTanggal::count();
        $totalRecordswithFilter = DB::table('m_tujuan')
        ->when($searchValue, function ($q) use ($searchValue) {
            return $q->where('m_tujuan.tujuan_kode', 'like', '%' .$searchValue . '%')
                         ->orWhere('m_tujuan.tujuan_inisial', 'like', '%' . $searchValue . '%')
                         ->orWhere('m_tujuan.tujuan_nama', 'like', '%' . $searchValue . '%');
        })
        ->count();

        // Fetch records
        $records = DB::table('m_tujuan')
            ->when($searchValue, function ($q) use ($searchValue) {
                return $q->where('m_tujuan.tujuan_kode', 'like', '%' .$searchValue . '%')
                            ->orWhere('m_tujuan.tujuan_inisial', 'like', '%' . $searchValue . '%')
                            ->orWhere('m_tujuan.tujuan_nama', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->orderBy($columnName,$columnSortOrder)
            ->orderBy('tujuan_kode','asc')
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $record->id;
            $kode = $record->tujuan_kode;
            $inisial = $record->tujuan_inisial;
            $nama = $record->tujuan_nama;
            if (Auth::user()->user_level == 'admin')
            {
                if ($record->tujuan_kode <= 2)
                {
                    $aksi = '';
                }
                else
                {
                    $aksi ='
                            <div class="btn-group">
                            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ti-settings"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" data-id="'.$record->id.'" data-kode="'.$record->tujuan_kode.'" data-toggle="modal" data-target="#EditTujuan">Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-id="'.$record->id.'" data-kode="'.$record->tujuan_kode.'" data-toggle="modal" data-target="#HapusTujuan">Hapus</a>
                            </div>
                            </div>
                            ';
                }

            }
            else
            {
                $aksi ='';
            }
            $data_arr[] = array(
                "id" => $id,
                "tujuan_kode"=>$kode,
                "tujuan_inisial"=>$inisial,
                "tujuan_nama"=> $nama,
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
    public function SimpanTujuan(Request $request)
    {

        $arr = array(
            'status'=>false,
            'hasil'=>'Data tidak disimpan'
        );
        return Response()->json($arr);
    }
    public function kalendar()
    {
        $tahun = date('Y')-1;
        $data = AppTanggal::whereYear('tanggal_angka','>=',$tahun)->whereIn('tanggal_jenis',['kerja','libur'])->orderBy('tanggal_angka','asc')->get();
        //dd($data);
        foreach ($data as $item) {
            if ($item->tanggal_jenis == 'libur')
            {
                $arr[]=array (
                    'title' => $item->tanggal_deskripsi,
                    'start' => $item->tanggal_angka,
                    'end' => $item->tanggal_angka,
                    'className' => 'bg-danger'
                );
            }
            else
            {
                if ($item->tanggal_petugas1_uid != null)
                {
                    $arr[]=array (
                        'title' => $item->Petugas1->name,
                        'start' => $item->tanggal_angka,
                        'end' => $item->tanggal_angka,
                        'className' => 'bg-success'
                    );
                }
                if ($item->tanggal_petugas2_uid != null)
                {
                    $arr[]=array (
                        'title' => $item->Petugas2->name,
                        'start' => $item->tanggal_angka,
                        'end' => $item->tanggal_angka,
                        'className' => 'bg-info'
                    );
                }

            }
        }
        $data_jadwal = json_encode($arr);
        return view('master.kalendar',['data_jadwal'=>$data_jadwal]);
    }
}
