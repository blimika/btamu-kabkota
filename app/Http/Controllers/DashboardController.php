<?php

namespace App\Http\Controllers;

use App\Kunjungan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $data_bulan = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );
        $data_bulan_pendek = array(
            1 => 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'
        );
        $tahun = Carbon::today()->format('Y');
        $bulan = (int) Carbon::today()->format('m');
        $nama_bulan_pendek = $data_bulan_pendek[$bulan];
        $nama_bulan = $data_bulan[$bulan];
        $DataKunjungan = Kunjungan::orderBy('kunjungan_tanggal','desc')->take(10)->get();
        return view('depan',[
            'tahun'=>$tahun,
            'nama_bulan_pendek'=>$nama_bulan_pendek,
            'nama_bulan'=>$nama_bulan,
            'bulan'=>$bulan,
            'DataKunjungan'=>$DataKunjungan
        ]);
    }
}
