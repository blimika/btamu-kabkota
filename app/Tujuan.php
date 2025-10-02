<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tujuan extends Model
{
    protected $table = 'm_tujuan';
    public $timestamps = false;
    protected $fillable = [
        'tujuan_kode',
        'tujuan_inisial',
        'tujuan_nama',
        'tujuan_tipe',
    ];
}
