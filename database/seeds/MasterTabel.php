<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Helpers\Generate;

class MasterTabel extends Seeder
{
    /**
     * Run the database seeds. Mall Pelayanan Publik
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_tujuan')->insert([
            ['id'=>1, 'tujuan_kode' => 1, 'tujuan_inisial'=>'KTR', 'tujuan_nama' => 'Kantor', 'tujuan_tipe' => 'kunjungan'],
            ['id'=>2, 'tujuan_kode' => 2, 'tujuan_inisial'=>'PST', 'tujuan_nama' => 'Pelayanan Statistik Terpadu', 'tujuan_tipe' => 'kunjungan'],
            ['id'=>3, 'tujuan_kode' => 3, 'tujuan_inisial'=>'POT', 'tujuan_nama' => 'Pojok Statistik', 'tujuan_tipe' => 'permintaan'],
            ['id'=>4, 'tujuan_kode' => 4, 'tujuan_inisial'=>'MPP', 'tujuan_nama' => 'Mall Pelayanan Publik', 'tujuan_tipe' => 'kunjungan'],
            ['id'=>5, 'tujuan_kode' => 5, 'tujuan_inisial'=>'EML', 'tujuan_nama' => 'E-Mail', 'tujuan_tipe' => 'permintaan'],
            ['id'=>6, 'tujuan_kode' => 6, 'tujuan_inisial'=>'WAP', 'tujuan_nama' => 'WhatsApp', 'tujuan_tipe' => 'permintaan'],
            ['id'=>7, 'tujuan_kode' => 7, 'tujuan_inisial'=>'TEL', 'tujuan_nama' => 'Telepon/Lainnya', 'tujuan_tipe' => 'permintaan'],


        ]);
        DB::table('m_pendidikan')->insert([
            ['id'=>1, 'pendidikan_kode' => 1, 'pendidikan_nama' => '<=SMA Sederajat'],
            ['id'=>2, 'pendidikan_kode' => 2, 'pendidikan_nama' => 'Diploma'],
            ['id'=>3, 'pendidikan_kode' => 3, 'pendidikan_nama' => 'Sarjana'],
            ['id'=>4, 'pendidikan_kode' => 4, 'pendidikan_nama' => 'Magister'],
            ['id'=>5, 'pendidikan_kode' => 5, 'pendidikan_nama' => 'Doktor'],
        ]);
        DB::table('m_layanan_pst')->insert([
            ['id'=>1, 'layanan_pst_kode' => 1, 'layanan_pst_inisial'=>'PS', 'layanan_pst_nama' => 'Perpustakaan'],
            ['id'=>2, 'layanan_pst_kode' => 2, 'layanan_pst_inisial'=>'PJ', 'layanan_pst_nama' => 'Produk Statistik Berbayar'],
            ['id'=>3, 'layanan_pst_kode' => 3,  'layanan_pst_inisial'=>'KS', 'layanan_pst_nama' => 'Konsultasi Statistik'],
            ['id'=>4, 'layanan_pst_kode' => 4,  'layanan_pst_inisial'=>'RS', 'layanan_pst_nama' => 'Rekomendasi Kegiatan Statistik'],
            ['id'=>5, 'layanan_pst_kode' => 99, 'layanan_pst_inisial'=>'LA', 'layanan_pst_nama' => 'Lainnya'],
        ]);
        DB::table('m_layanan_kantor')->insert([
            ['id'=>1, 'layanan_kantor_kode' => 1, 'layanan_kantor_inisial'=>'PG', 'layanan_kantor_nama' => 'Pengaduan'],
            ['id'=>2, 'layanan_kantor_kode' => 2, 'layanan_kantor_inisial'=>'KL', 'layanan_kantor_nama' => 'Konsultasi'],
            ['id'=>3, 'layanan_kantor_kode' => 3,  'layanan_kantor_inisial'=>'PW', 'layanan_kantor_nama' => 'Penawaran'],
            ['id'=>4, 'layanan_kantor_kode' => 99, 'layanan_kantor_inisial'=>'LB', 'layanan_kantor_nama' => 'Lainnya'],
        ]);
        DB::table('m_akses')->insert([
            ['id'=>1, 'akses_ip' => '127.0.0.1', 'akses_flag' => '1','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['id'=>2, 'akses_ip' => '::1', 'akses_flag' => '1','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['id'=>3, 'akses_ip' => '36.95.114.173', 'akses_flag' => '1','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['id'=>4, 'akses_ip' => '36.95.114.170', 'akses_flag' => '1','created_at' => Carbon::now()->format('Y-m-d H:i:s'),'updated_at' => Carbon::now()->format('Y-m-d H:i:s')],
        ]);
        DB::table('m_bulan')->insert([
            ['id' => 1, 'bulan_nama_pendek' => 'Jan', 'bulan_nama' => 'Januari'],
            ['id' => 2, 'bulan_nama_pendek' => 'Feb', 'bulan_nama' => 'Februari'],
            ['id' => 3, 'bulan_nama_pendek' => 'Mar', 'bulan_nama' => 'Maret'],
            ['id' => 4, 'bulan_nama_pendek' => 'Apr', 'bulan_nama' => 'April'],
            ['id' => 5, 'bulan_nama_pendek' => 'Mei', 'bulan_nama' => 'Mei'],
            ['id' => 6, 'bulan_nama_pendek' => 'Jun', 'bulan_nama' => 'Juni'],
            ['id' => 7, 'bulan_nama_pendek' => 'Jul', 'bulan_nama' => 'Juli'],
            ['id' => 8, 'bulan_nama_pendek' => 'Agu', 'bulan_nama' => 'Agustus'],
            ['id' => 9, 'bulan_nama_pendek' => 'Sep', 'bulan_nama' => 'September'],
            ['id' => 10, 'bulan_nama_pendek' => 'Okt', 'bulan_nama' => 'Oktober'],
            ['id' => 11, 'bulan_nama_pendek' => 'Nov', 'bulan_nama' => 'November'],
            ['id' => 12, 'bulan_nama_pendek' => 'Des', 'bulan_nama' => 'Desember'],
        ]);
        DB::table('users')->insert([
            'user_uid' => Generate::Kode(6),
            'name' => 'Admin Sistem',
            'username' => 'admin',
            'email' => 'admin@statsntb.id',
            'password' => bcrypt('1'),
            'user_level' => 'admin',
            'user_telepon' => '081237802900',
            'user_flag' => 'aktif'
        ]);
    }
}
