<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\User;
use App\Helpers\Generate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Pengunjung;

class ImportPengunjung implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $cekdata = Pengunjung::where('pengunjung_nomor_hp',$row['nomor_hp'])->first();
            if (!$cekdata)
                {
                    $data = new Pengunjung();
                    $data->pengunjung_uid = Generate::Kode(6);
                    $data->pengunjung_nama = $row['nama_lengkap'];
                    $data->pengunjung_nomor_hp = $row['nomor_hp'];
                    $data->pengunjung_tahun_lahir = $row['tahun_lahir'];
                    $data->pengunjung_jenis_kelamin = $row['jenis_kelamin'];
                    $data->pengunjung_pekerjaan = $row['pekerjaan'];
                    $data->pengunjung_pendidikan = $row['pendidikan'];
                    $data->pengunjung_email = $row['email'];
                    $data->pengunjung_alamat = $row['alamat'];
                    $data->pengunjung_total_kunjungan = 0;
                    //$data->pengunjung_user_uid = 0; kedepan untuk member
                    $data->save();
                }
            else
            {
                //update datanya aja
                $data = Pengunjung::where('pengunjung_nomor_hp',$row['nomor_hp'])->first();
                $data->pengunjung_nama = $row['nama_lengkap'];
                $data->pengunjung_nomor_hp = $row['nomor_hp'];
                $data->pengunjung_tahun_lahir = $row['tahun_lahir'];
                $data->pengunjung_jenis_kelamin = $row['jenis_kelamin'];
                $data->pengunjung_pekerjaan = $row['pekerjaan'];
                $data->pengunjung_pendidikan = $row['pendidikan'];
                $data->pengunjung_email = $row['email'];
                $data->pengunjung_alamat = $row['alamat'];
                $data->update();
            }
        }
    }
    public function batchSize(): int
    {
        return 1000;
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}
