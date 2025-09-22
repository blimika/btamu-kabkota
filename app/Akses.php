<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Akses extends Model
{
    protected $table = 'm_akses';
    protected $fillable = ['akses_ip','akses_flag'];
}
