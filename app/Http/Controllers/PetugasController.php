<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PetugasController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('login.index');
    }
    public function login(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'username'    => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. Simpan kredensial
        $credentials = $request->only('username', 'password');

        // 3. Coba lakukan autentikasi
        if (Auth::attempt($credentials)) {
            // Jika berhasil
            $user = Auth::user();
            $user->user_last_login = Carbon::now()->toDateTimeString(); // Menggunakan helper now() untuk waktu saat ini
            $user->user_last_ip = $request->getClientIp(); // Mengambil IP dari request
            $user->save(); // 3. Simpan perubahan ke database
            $request->session()->regenerate(); // Regenerate session untuk keamanan
            return redirect()->intended('/'); // Redirect ke halaman yang dituju sebelumnya atau ke /dashboard
        }

        // 4. Jika gagal
        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->withInput(); // Kembali ke halaman login dengan pesan error
    }

    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
