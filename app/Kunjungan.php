<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'm_kunjungan';
    protected $primaryKey = 'kunjungan_id';
    protected $fillable = [
        'pengunjung_uid',
        'kunjungan_uid',
        'kunjungan_tanggal',
        'kunjungan_keperluan',
        'kunjungan_tujuan',
        'kunjungan_layanan_pst',
        'kunjungan_layanan_kantor',
        'kunjungan_foto',
        'kunjungan_jenis',
        'kunjungan_jumlah_orang',
        'kunjungan_jumlah_pria',
        'kunjungan_jumlah_wanita',
        'kunjungan_petugas_uid'
    ];
    public function Pengunjung(){
    	return $this->belongsTo('App\Pengunjung', 'pengunjung_uid', 'pengunjung_uid');
    }
    public function Tujuan()
    {
        return $this->belongsTo('App\Tujuan', 'kunjungan_tujuan', 'tujuan_kode');
    }
    public function LayananPst(){
    	return $this->belongsTo('App\LayananPst', 'kunjungan_layanan_pst', 'layanan_pst_kode');
    }
    public function LayananKantor(){
    	return $this->belongsTo('App\LayananKantor', 'kunjungan_layanan_kantor', 'layanan_kantor_kode');
    }
    public function Petugas()
    {
        return $this->belongsTo('App\User', 'kunjungan_petugas_uid', 'user_uid');
    }
}
