<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_uid',
        'name',
        'username',
        'email',
        'ganti_email',
        'email_kodever',
        'user_foto',
        'password',
        'user_level',
        'user_last_login',
        'user_last_ip',
        'user_flag'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function Kunjungan()
    {
        return $this->hasMany('App\Kunjungan','kunjungan_petugas_uid','user_uid')->orderBy('kunjungan_tanggal','desc')->take(15);
    }
}
