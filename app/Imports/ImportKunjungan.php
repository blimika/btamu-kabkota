<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Tanggal;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\User;
use App\Pengunjung;
use App\Kunjungan;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Generate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Tujuan;
use App\LayananPst;
use App\LayananKantor;

class ImportKunjungan implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function collection(Collection $rows)
    {
        $error = 0;
        $sukses = 0;
        foreach ($rows as $row)
        {
            //cek dulu nomor hpnya, kalo belum ada tambahkan, kalo sudah ada langsung ambil
            $data = Pengunjung::where('pengunjung_nomor_hp',$row['nomor_hp'])->first();
            if (!$data)
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

            //nomor antrian dulu
            $cek_kunjungan = Kunjungan::where([['pengunjung_uid', $data->pengunjung_uid], ['kunjungan_tanggal', Carbon::parse($row['tanggal'])->format('Y-m-d')], ['kunjungan_tujuan', $row['tujuan']]])->count();
            if ($cek_kunjungan > 0)
            {
                //data kunjunganan sudah di imput
                $error++;
            }
            else
            {
                //nomor antrian
                if ($row['tujuan'] == 1)
                {
                    //kantor
                    $data_antrian = Kunjungan::where([['kunjungan_tanggal', Carbon::parse($row['tanggal'])->format('Y-m-d')],['kunjungan_tujuan',$row['tujuan']], ['kunjungan_layanan_kantor', $row['layanan_kantor']]])->orderBy('kunjungan_nomor_antrian', 'desc')->first();
                    $data_layanan_utama = LayananKantor::where('layanan_kantor_kode',$row['layanan_kantor'])->first();
                    $nomor_antrian_inisial = $data_layanan_utama->layanan_kantor_inisial;
                    $layanan_pst = 99;
                    $layanan_kantor = $row['layanan_kantor'];
                }
                elseif ($row['tujuan'] == 2)
                {
                    //pst
                    $data_antrian = Kunjungan::where([['kunjungan_tanggal', Carbon::parse($row['tanggal'])->format('Y-m-d')],['kunjungan_tujuan',$row['tujuan']], ['kunjungan_layanan_pst', $row['layanan_pst']]])->orderBy('kunjungan_nomor_antrian', 'desc')->first();
                    $data_layanan_utama = LayananPst::where('layanan_pst_kode',$row['layanan_pst'])->first();
                    $nomor_antrian_inisial = $data_layanan_utama->layanan_pst_inisial;
                    $layanan_pst = $row['layanan_pst'];
                    $layanan_kantor = 99;
                }
                else
                {
                    //selain kantor dan pst
                    $data_antrian = Kunjungan::where([['kunjungan_tanggal', Carbon::parse($row['tanggal'])->format('Y-m-d')], ['kunjungan_tujuan',$row['tujuan']]])->orderBy('kunjungan_nomor_antrian', 'desc')->first();
                    $layanan_pst = 99;
                    $layanan_kantor = 99;
                    $data_layanan_utama = Tujuan::where('tujuan_kode',$row['tujuan'])->first();
                    $nomor_antrian_inisial = $data_layanan_utama->tujuan_inisial;
                }
                //batas tujuan
                //mulai antrian
                if ($data_antrian) {
                    //kalo sudah ada antrian
                    $nomor_selanjutnya = $data_antrian->kunjungan_nomor_antrian + 1;
                }
                else {
                    //belum ada sama sekali
                    $nomor_selanjutnya = 1;
                }
                //cek jenis kelamin ambil dari query data diatas
                if ($data->pengunjung_jenis_kelamin == 'laki_laki') {
                    $laki = 1;
                    $wanita = 0;
                } else {
                    $laki = 0;
                    $wanita = 1;
                }
                $jam_datang = Carbon::parse($row['tanggal'] .' '.$row['jam_mulai'].':00')->format('Y-m-d H:i:s');
                $jam_pulang = Carbon::parse($row['tanggal'] .' '.$row['jam_selesai'].':00')->format('Y-m-d H:i:s');
                $data_petugas = User::where('username',$row['petugas'])->first();
                if ($data_petugas)
                {
                    $petugas_uid = $data_petugas->user_uid;
                    $petugas_username = $row['petugas'];
                }
                else
                {
                    $petugas_uid = Auth::user()->user_uid;
                    $petugas_username = Auth::user()->username;
                }
                $loket_petugas = 1;
                //tambahkan ke tabel kunjungan
                //tambahkan ke tabel m_kunjungan
                $newdata = new Kunjungan();
                $newdata->pengunjung_uid = $data->pengunjung_uid;
                $newdata->kunjungan_uid = Generate::Kode(7);
                $newdata->kunjungan_tanggal = Carbon::parse($row['tanggal'])->format('Y-m-d');
                $newdata->kunjungan_keperluan = $row['keperluan'];
                $newdata->kunjungan_jenis = 'perorangan';
                $newdata->kunjungan_tujuan = $row['tujuan'];
                $newdata->kunjungan_layanan_pst = $layanan_pst;
                $newdata->kunjungan_layanan_kantor = $layanan_kantor;
                $newdata->kunjungan_jumlah_orang = 1;
                $newdata->kunjungan_jumlah_pria = $laki;
                $newdata->kunjungan_jumlah_wanita = $wanita;
                $newdata->kunjungan_nomor_antrian = $nomor_selanjutnya;
                $newdata->kunjungan_teks_antrian = $nomor_antrian_inisial . '-' . sprintf("%03d", $nomor_selanjutnya);
                $newdata->kunjungan_jam_datang = $jam_datang;
                $newdata->kunjungan_jam_pulang = $jam_pulang;
                $newdata->kunjungan_flag_antrian = 'selesai';
                $newdata->kunjungan_loket_petugas = $loket_petugas;
                $newdata->kunjungan_flag_feedback = 'sudah';
                $newdata->kunjungan_nilai_feedback = $row['nilai_feedback'];
                $newdata->kunjungan_sarpras_feedback = $row['nilai_sarpras'];
                $newdata->kunjungan_komentar_feedback = $row['komentar_feedback'];
                $newdata->kunjungan_tanggal_feedback = NOW();
                $newdata->kunjungan_petugas_uid = $petugas_uid;
                $newdata->kunjungan_petugas_username = $petugas_username;
                $newdata->save();
                //tambah total kunjungan di tabel pengunjung kunjungan_tanggal_feedback
                //ambil dari tabel m_pengunjung
                $total_kunjungan = $data->pengunjung_total_kunjungan;
                $data->pengunjung_total_kunjungan = $total_kunjungan + 1;
                $data->update();
                $sukses++;
            }
            //tambahkan kunjungannya.
        }
        //return $pesan = 'Data kunjungan error ada '.$error.' dan sukses ada '.$sukses;
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
