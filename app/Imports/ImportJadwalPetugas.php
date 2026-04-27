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
            $data = Tanggal::where([['tanggal_angka',$row['tanggal']],['tanggal_jenis','kerja']])->first();
            if ($data)
            {
                $cek_user1 = User::where('username', trim($row['petugas1_username']))->first();
                $cek_user2 = User::where('username', trim($row['petugas2_username']))->first();
                $cek_user3 = User::where('username', trim($row['petugas3_username']))->first();

                $data->tanggal_petugas1_uid = $cek_user1 ? $cek_user1->user_uid : null;
                $data->tanggal_petugas2_uid = $cek_user2 ? $cek_user2->user_uid : null;
                $data->tanggal_petugas3_uid = $cek_user3 ? $cek_user3->user_uid : null;
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
