<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tanggal extends Model
{
    protected $table = 'm_tanggal';
    public function Petugas1()
    {
        return $this->belongsTo('App\User', 'tanggal_petugas1_uid', 'user_uid');
    }
    public function Petugas2()
    {
        return $this->belongsTo('App\User', 'tanggal_petugas2_uid', 'user_uid');
    }
}
