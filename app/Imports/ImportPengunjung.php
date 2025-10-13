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
                    $data = new User();
                    $data->user_uid = Generate::Kode(6);
                    $data->username = $row['username'];
                    $data->name = $row['name'];
                    $data->email = $row['email'];
                    $data->password = bcrypt($row['password']);
                    $data->user_level = $row['user_level'];
                    $data->user_telepon = $row['user_telepon'];
                    $data->ganti_email = $row['email'];
                    $data->email_kodever = '0';
                    $data->user_flag = 'aktif';
                    $data->save();
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
