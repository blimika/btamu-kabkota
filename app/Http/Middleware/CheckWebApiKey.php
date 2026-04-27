<?php

namespace App\Http\Middleware;

use Closure;

class CheckWebApiKey
{
    public function handle($request, Closure $next)
    {
        // Ambil key yang valid dari file .env
        $validKey = env('WEBAPI_KEY');

        // Cek apakah parameter 'key' ada di URL dan nilainya cocok
        if ($request->query('key') !== $validKey) {
            // Jika tidak cocok atau tidak ada, kembalikan response 401 Unauthorized
            return response()->json([
                'status' => 'false',
                'message' => 'Unauthorized: Invalid or missing API Key'
            ], 401);
        }

        // Jika cocok, lanjutkan request ke Controller
        return $next($request);
    }
}
