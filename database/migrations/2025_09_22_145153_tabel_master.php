<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TabelMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_bulan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('bulan_nama_pendek',3);
            $table->string('bulan_nama',20);
        });
        Schema::create('m_akses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('akses_ip', 20);
            $table->boolean('akses_flag')->default(0);
            $table->timestamps();
        });
        Schema::create('m_tanggal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal_angka');
            $table->string('tanggal_hari',6)->nullable();
            $table->enum('tanggal_jenis',['kerja','sabtu_minggu','libur'])->default('kerja');
            $table->string('tanggal_deskripsi',250)->nullable();
            $table->string('tanggal_petugas1_uid',6)->nullable();
            $table->string('tanggal_petugas2_uid',6)->nullable();
            $table->timestamps();
        });
        Schema::create('m_tujuan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->boolean('tujuan_kode')->unsigned(); //1 Kantor, 2 pst , 3 pojok statistik, 4, email, 5 wa, 6 telepon/lainnya
            $table->string('tujuan_inisial',3);
            $table->string('tujuan_nama',200);
        });
        Schema::create('m_layanan_pst', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->boolean('layanan_pst_kode')->unsigned(); // 1 perpus, 2 penjualan, 3 konsultasi, 4 rekomendasi
            $table->string('layanan_pst_inisial',2);
            $table->string('layanan_pst_nama',100);
        });
        Schema::create('m_pendidikan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->boolean('pendidikan_kode')->unsigned();
            $table->string('pendidikan_nama',100);
        });
        Schema::create('m_layanan_kantor', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->boolean('layanan_kantor_kode')->unsigned(); // 1 Pengaduan 2 Konsultasi 3 Penawaran 99 Lainnya
            $table->string('layanan_kantor_inisial',2);
            $table->string('layanan_kantor_nama',100);
        });
        Schema::create('m_pengunjung', function (Blueprint $table) {
            $table->bigIncrements('pengunjung_id');
            $table->string('pengunjung_uid',6)->unique()->nullable();
            $table->string('pengunjung_nama',250);
            $table->string('pengunjung_nomor_hp',20);
            $table->year('pengunjung_tahun_lahir')->nullable();
            $table->enum('pengunjung_jenis_kelamin',['laki_laki','perempuan'])->default('laki_laki');
            $table->string('pengunjung_pekerjaan',254)->nullable();
            $table->tinyInteger('pengunjung_pendidikan')->nullable();
            $table->string('pengunjung_email',254)->nullable();
            $table->longText('pengunjung_alamat')->nullable();
            $table->string('pengunjung_foto_profil',250)->nullable();
            $table->tinyInteger('pengunjung_total_kunjungan')->unsigned()->default(0);
            $table->string('pengunjung_user_uid',6)->nullable();
            $table->timestamps();
        });
        Schema::create('m_kunjungan', function (Blueprint $table) {
            $table->bigIncrements('kunjungan_id');
            $table->string('pengunjung_uid',6)->nullable();
            $table->string('kunjungan_uid',7)->unique()->nullable();
            $table->date('kunjungan_tanggal');
            $table->text('kunjungan_keperluan')->nullable();
            $table->text('kunjungan_tindak_lanjut')->nullable();
            $table->enum('kunjungan_jenis',['perorangan','kelompok'])->default('perorangan');
            $table->tinyInteger('kunjungan_tujuan')->default(1); //1 Kantor, 2 pst , 3 pojok statistik, 4
            $table->tinyInteger('kunjungan_layanan_pst')->default(99); //kalo tujuan kantor otomatis layanan_pst 99
            $table->tinyInteger('kunjungan_layanan_kantor')->default(99); //kalo tujuan pst otomatis layanan_kantor 99
            $table->string('kunjungan_foto',250)->nullable();
            $table->tinyInteger('kunjungan_jumlah_orang')->default(1);
            $table->tinyInteger('kunjungan_jumlah_pria')->default(0); //tamu laki
            $table->tinyInteger('kunjungan_jumlah_wanita')->default(0);
            $table->enum('kunjungan_flag_feedback',['belum','sudah'])->default('belum');
            $table->tinyInteger('kunjungan_nilai_feedback')->default(0); // skala 1 - 6
            $table->text('kunjungan_komentar_feedback')->nullable();
            $table->string('kunjungan_ip_feedback',20)->nullable();
            $table->string('kunjungan_agent_feedback',255)->nullable();
            $table->dateTime('kunjungan_tanggal_feedback')->nullable();
            $table->tinyInteger('kunjungan_nomor_antrian')->unsigned()->default(0);
            $table->string('kunjungan_teks_antrian',15)->nullable();
            $table->boolean('kunjungan_loket_petugas')->default(1);
            $table->enum('kunjungan_flag_antrian',['antrian','dalam_layanan','selesai'])->default('antrian');
            $table->dateTime('kunjungan_jam_datang')->nullable();
            $table->dateTime('kunjungan_jam_pulang')->nullable();
            $table->string('kunjungan_petugas_uid',6)->nullable();
            $table->timestamps();
        });
        Schema::create('m_whatsapp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('wa_tanggal');
            $table->string('wa_uid',8)->nullable();
            $table->string('wa_pengunjung_uid',6)->nullable();
            $table->string('wa_kunjungan_uid',7)->nullable();
            $table->string('wa_user_uid',6)->nullable();
            $table->string('wa_device',30)->nullable();
            $table->string('wa_target',30)->nullable();
            $table->string('wa_message_id',255)->nullable();
            $table->text('wa_message')->nullable();
            $table->text('wa_status')->nullable();
            $table->enum('wa_flag',['antrian','terkirim','gagal'])->default('antrian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_bulan');
        Schema::dropIfExists('m_akses');
        Schema::dropIfExists('m_tanggal');
        Schema::dropIfExists('m_tujuan');
        Schema::dropIfExists('m_layanan_pst');
        Schema::dropIfExists('m_pendidikan');
        Schema::dropIfExists('m_layanan_kantor');
        Schema::dropIfExists('m_pengunjung');
        Schema::dropIfExists('m_kunjungan');
        Schema::dropIfExists('m_whatsapp');
    }
}
