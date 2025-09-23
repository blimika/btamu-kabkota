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

class WebapiController extends Controller
{
    public function WebApi(Request $request)
    {
        /*
        //key=register
        model=hp
        model=pengunjung
        model=kunjungan
        id=xxx
        */
        $arr = array(
            'status'=>false,
            'message'=>'Data tidak tersedia',
            'data'=>'Webapi Bukutamu v4.0 (Kabkota)'
        );
        if ($request->model == "hp")
        {
            //cari nomor hp
            $data = Pengunjung::with('Pendidikan','Kunjungan','Kunjungan.Tujuan','Kunjungan.LayananPst','Kunjungan.LayananKantor','Kunjungan.Petugas')->where('pengunjung_nomor_hp',$request->nomor)->first();
            if ($data)
            {
                $arr = array(
                    'status'=>true,
                    'message'=>'Data ditemukan',
                    'data'=>$data
                );

            }
        }

        if ($request->model == "pengunjung")
        {
            //pengunjung
            $data = Pengunjung::with('Pendidikan','Kunjungan','Kunjungan.Tujuan','Kunjungan.LayananPst','Kunjungan.LayananKantor','Kunjungan.Petugas')->where('pengunjung_uid',$request->uid)->first();
            if ($data)
            {
                $arr = array(
                    'status'=>true,
                    'message'=>'Data tersedia',
                    'data'=>$data
                );
            }
            else
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Data tidak tersedia',
                    'data'=>null
                );
            }

        }
        if ($request->model == "petugas")
        {
            //member/users
            $data = User::with('mLevel','Pengunjung','Pengunjung.Pendidikan','Pengunjung.JenisKelamin','Pengunjung.Kunjungan','Pengunjung.Kunjungan.Tujuan','Pengunjung.Kunjungan.JenisKunjungan','Pengunjung.Kunjungan.LayananUtama','Pengunjung.Kunjungan.LayananKantor','Pengunjung.Kunjungan.FlagAntrian')->where('id',$request->id)->first();
            //$data = Pengunjung::with('Pendidikan','JenisKelamin','Member','Kunjungan','Kunjungan.Tujuan','Kunjungan.JenisKunjungan','Kunjungan.LayananUtama','Kunjungan.FlagAntrian')->where('pengunjung_uid',$request->uid)->first();
            if ($data)
            {
                $arr = array(
                    'status'=>true,
                    'message'=>'Data tersedia',
                    'data'=>$data
                );
            }
            else
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Data tidak tersedia',
                    'data'=>null
                );
            }

        }
        if ($request->model == "kunjungan")
        {
            //kunjungan
            $data = Kunjungan::with('Pengunjung','Tujuan','LayananPst','LayananKantor','Petugas','Pengunjung.Pendidikan')->where('kunjungan_uid',$request->uid)->first();
            if ($data)
            {
                $arr = array(
                    'status'=>true,
                    'message'=>'Data tersedia',
                    'data'=>$data
                );
            }
            else
            {
                $arr = array(
                    'status'=>false,
                    'message'=>'Data tidak tersedia',
                    'data'=>null
                );
            }

        }

        return Response()->json($arr);
    }
}
