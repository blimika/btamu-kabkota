<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Tambahkan ini
use Illuminate\Support\Facades\DB;   // <-- Tambahkan ini

class CheckIpOrLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kondisi 1: Cek apakah pengguna sudah login
        if (Auth::check()) {
            // Jika sudah login, langsung izinkan akses
            return $next($request);
        }

        // Jika belum login, lanjut ke kondisi 2: Cek IP Address
        $clientIp = $request->ip(); // Dapatkan IP pengguna

        // Query ke database untuk mengecek apakah IP terdaftar
        $isIpAllowed = DB::table('m_akses')->where('akses_ip', $clientIp)->exists();

        if ($isIpAllowed) {
            // Jika IP terdaftar di tabel, izinkan akses
            return $next($request);
        }

        // Jika kedua kondisi tidak terpenuhi, tolak akses
        // abort() akan menghentikan proses dan menampilkan halaman error
        abort(403, 'AKSES DITOLAK. Alamat IP Anda tidak terdaftar atau Anda belum login.');
    }
}
