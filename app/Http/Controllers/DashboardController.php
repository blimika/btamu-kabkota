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
        $kunjungan_hari_ini = Kunjungan::whereDate('kunjungan_tanggal',Carbon::today()->format('Y-m-d'))->count();
        $pengunjung_hari_ini = Kunjungan::whereDate('kunjungan_tanggal',Carbon::today()->format('Y-m-d'))->sum('kunjungan_jumlah_orang');
        //bulan
        $kunjungan_bulan_ini = Kunjungan::whereYear('kunjungan_tanggal',Carbon::today()->format('Y'))->whereMonth('kunjungan_tanggal',Carbon::today()->format('m'))->count();
        $pengunjung_bulan_ini = Kunjungan::whereYear('kunjungan_tanggal',Carbon::today()->format('Y'))->whereMonth('kunjungan_tanggal',Carbon::today()->format('m'))->sum('kunjungan_jumlah_orang');
        //tahun
        $kunjungan_tahun_ini = Kunjungan::whereYear('kunjungan_tanggal',Carbon::today()->format('Y'))->count();
        $pengunjung_tahun_ini = Kunjungan::whereYear('kunjungan_tanggal',Carbon::today()->format('Y'))->sum('kunjungan_jumlah_orang');

        $DataKunjungan = Kunjungan::orderBy('kunjungan_tanggal','desc')->take(10)->get();
        return view('depan',[
            'tahun'=>$tahun,
            'nama_bulan_pendek'=>$nama_bulan_pendek,
            'nama_bulan'=>$nama_bulan,
            'bulan'=>$bulan,
            'DataKunjungan'=>$DataKunjungan,
            'kunjungan_hari_ini'=>$kunjungan_hari_ini,
            'pengunjung_hari_ini'=>$pengunjung_hari_ini,
            'kunjungan_bulan_ini'=>$kunjungan_bulan_ini,
            'pengunjung_bulan_ini'=>$pengunjung_bulan_ini,
            'kunjungan_tahun_ini'=>$kunjungan_tahun_ini,
            'pengunjung_tahun_ini'=>$pengunjung_tahun_ini,
        ]);
    }
}
