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
            $cek_user1 = User::where('user_uid',trim($row['petugas1_uid']))->first();
            $cek_user2 = User::where('user_uid',trim($row['petugas2_uid']))->first();
            $data = Tanggal::where([['tanggal_angka',$row['tanggal']],['tanggal_jenis','kerja']])->first();
            if ($data)
            {
                if ($cek_user1)
                {
                    if ($cek_user2)
                    {
                        $petugas2_uid = $cek_user2->user_uid;
                    }
                    else
                    {
                        $petugas2_uid = null;
                    }
                    $petugas1_uid = $cek_user1->user_uid;
                }
                else
                {
                    if ($cek_user2)
                    {
                        $petugas2_uid = $cek_user2->user_uid;
                    }
                    else
                    {
                        $petugas2_uid = null;
                    }
                    $petugas1_uid = null;
                }
                $data->tanggal_petugas1_uid = $petugas1_uid;
                $data->tanggal_petugas2_uid = $petugas2_uid;
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
