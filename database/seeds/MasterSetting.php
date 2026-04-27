<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Helpers\Generate;

class MasterSetting extends Seeder
{
    public function run()
    {
        DB::table('m_setting')->insert([
            ['id'=>1, 'key' => 'APP_LINK_SKD', 'value' => 'https://skd.bps.go.id/skd/p/5271','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['id'=>2, 'key' => 'NAMA_SATKER', 'value' => 'BPS Kota Mataram','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['id'=>3, 'key' => 'ALAMAT_SATKER', 'value' => 'Jalan Jendral Sudirman No. 71 Mataram NTB 83124','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['id'=>4, 'key' => 'WA_SATKER', 'value' => '081139043333','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['id'=>5, 'key' => 'EMAIL_SATKER', 'value' => 'ipds5271@bps.go.id','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['id'=>6, 'key' => 'URL_SATKER', 'value' => 'https://mataramkota.bps.go.id','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
        ]);
    }
}
