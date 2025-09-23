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

class KunjunganController extends Controller
{
    public function tambah()
    {
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
    public function simpan(Request $request)
    {
        dd($request->all());
    }
}
