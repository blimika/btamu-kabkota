<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Auth;
use App\Tanggal;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\User;

class ImportJadwalPetugas implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $cek_user1 = User::where('user_uid',$row['petugas1_uid'])->first();
            $cek_user2 = User::where('user_uid',$row['petugas2_uid'])->first();
            $data = Tanggal::where([['tanggal_angka',$row['tanggal']],['tanggal_jenis','kerja']])->first();
            if ($data)
            {
                if ($cek_user1)
                {
                    if ($cek_user2)
                    {
                        $petugas2_uid = $cek_user2->user_uid;
                        $petugas2_username = $cek_user2->username;
                    }
                    else
                    {
                        $petugas2_uid = null;
                        $petugas2_username = null;
                    }
                    $petugas1_uid = $cek_user1->user_uid;
                    $petugas1_username = $cek_user1->username;
                }
                else
                {
                    if ($cek_user2)
                    {
                        $petugas2_uid = $cek_user2->user_uid;
                        $petugas2_username = $cek_user2->username;
                    }
                    else
                    {
                        $petugas2_uid = null;
                        $petugas2_username = null;
                    }
                    $petugas1_uid = null;
                    $petugas1_username = NULL;
                }
                $data->petugas1_id = $petugas1_uid;
                $data->petugas2_id = $petugas2_uid;
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
