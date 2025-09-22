<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pengunjung extends Model
{
    protected $table = 'm_pengunjung';
    protected $primaryKey = 'pengunjung_id';
    protected $fillable = [
        'pengunjung_uid',
        'pengunjung_nama',
        'pengunjung_nomor_hp',
        'pengunjung_tahun_lahir',
        'pengunjung_jenis_kelamin',
        'pengunjung_pekerjaan',
        'pengunjung_pendidikan',
        'pengunjung_email',
        'pengunjung_alamat',
        'pengunjung_foto_profil',
        'pengunjung_total_kunjungan'
    ];
    public function Pendidikan(){
    	return $this->belongsTo('App\Pendidikan', 'pengunjung_pendidikan', 'pendidikan_kode');
    }
    public function Kunjungan()
    {
        return $this->hasMany('App\Kunjungan','pengunjung_uid','pengunjung_uid')->orderBy('kunjungan_tanggal','desc')->take(15);
    }
}
