<?php
    namespace App\Services;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;

    class WhatsAppService
    {
        protected $baseUrl;
        protected $BasicUser;
        protected $BasicPasswd;
        protected $DeviceID;

        public function __construct()
        {
            $this->baseUrl = env('APP_WA_LOKAL_URL');
            $this->BasicUser = env('APP_WA_LOKAL_AUTH_USER');
            $this->BasicPasswd = env('APP_WA_LOKAL_AUTH_PASSWD');
            $this->DeviceID = env('APP_WA_LOKAL_DEVICE_ID');
        }
        /**
     * Mengecek apakah nomor HP terdaftar di WhatsApp
     */
        public function formatNomorHp($nomor)
        {
            // 1. Bersihkan semua karakter selain angka (hapus spasi, strip -, tanda kurung (), dan tanda plus +)
            $bersih = preg_replace('/[^0-9]/', '', $nomor);

            // 2. Cek apakah nomor diawali dengan angka '0'
            if (substr($bersih, 0, 1) == '0') {
                // Ganti angka '0' di awal dengan '62'
                $bersih = '62' . substr($bersih, 1);
            }

            // (Opsional) Cek jika nomor tidak sengaja diketik tanpa 0 atau 62 di awal (misal langsung 812...)
            if (substr($bersih, 0, 2) != '62') {
                // Jika Anda yakin targetnya nomor Indonesia, bisa paksa tambah 62
                // $bersih = '62' . $bersih;
            }

            return $bersih;
        }
        public function checkNumber($phone)
        {
            $formattedPhone = $this->formatNomorHp($phone);

            // Pastikan setelah dicuci nomor tidak kosong (mencegah error jika input aslinya huruf semua)
            if (empty($formattedPhone)) {
                return [
                    'status'  => false,
                    'message' => 'Format nomor HP tidak valid.',
                ];
            }
            try {
                $url_check = $this->baseUrl . 'user/check';

                $response = Http::withBasicAuth($this->BasicUser, $this->BasicPasswd)
                    ->withHeaders([
                        'Accept'      => 'application/json',
                        'X-Device-Id' => $this->DeviceID,
                    ])
                    ->get($url_check, [
                        'phone' => $formattedPhone
                    ]);

                // Lemparkan exception jika gagal (4xx atau 5xx)
                $response->throw();

                // Ambil body response dalam bentuk array
                $data = $response->json();

                // Evaluasi apakah nomor ada WA-nya
                if (isset($data['results']['is_on_whatsapp']) && $data['results']['is_on_whatsapp'] == true) {
                    return [
                        'status'         => true,
                        'message'        => 'Nomor terdaftar di WhatsApp',
                        'formatted_phone'=> $formattedPhone // Kembalikan nomor bersih agar bisa disimpan ke DB jika perlu
                    ];
                }

                return [
                    'status'  => false,
                    'message' => 'Nomor tidak terdaftar di WhatsApp',
                ];

            } catch (\Throwable $e) {
                Log::error('WA Service [checkNumber] : ' . $e->getMessage());

                return [
                    'status'  => false,
                    'message' => 'Gagal terhubung ke API Check Number: ' . $e->getMessage(),
                ];
            }
        }
        /*
        public function GetDevice()
        {
            try {
                $url_base = $this->baseUrl.'app/devices';
                $response = Http::withBasicAuth($this->BasicUser, $this->BasicPasswd)->withHeaders([
                'accept' => 'accept: application/json',
                'X-Device-ID' => 'X-Device-Id: mataram5271',
                    ])->get($url_base);
            } catch (\Throwable $e) {
                $response = Log::error('Check Nomor HP WA : ' . $e->getMessage());
                //return response()->json(['error' => 'Internal Server Error'],500);
            }
            return $response;
        }
            */
        public function GetDevice()
        {
            try {
                $url_base = $this->baseUrl . 'app/devices';
                $response = Http::withBasicAuth($this->BasicUser, $this->BasicPasswd)
                    ->withHeaders([
                        'Accept'      => 'application/json',
                        'X-Device-Id' => $this->DeviceID,
                    ])
                    ->get($url_base);

                // Lemparkan exception jika status code response adalah 4xx atau 5xx
                $response->throw();

            } catch (\Throwable $e) {
                Log::error('Check Nomor HP WA : ' . $e->getMessage());

                // Return response JSON dengan status 500 agar aplikasi tidak crash
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Internal Server Error atau Gagal menghubungi API'
                ], 500);
            }

            // Jika sukses (status 2xx), kembalikan response aslinya
            return $response;
        }

        public function sendMessage($recipients, $message)
        {
            // 1. Panggil fungsi cek nomor yang baru saja kita buat
            $check = $this->checkNumber($recipients);

            // 2. Jika nomor TIDAK ADA di WA (status = false), langsung hentikan dan kembalikan error
            if ($check['status'] === false) {
                return response()->json([
                    'status'  => false,
                    'message' => $check['message']
                ]);
            }

            $formattedPhone = $check['formatted_phone'] ?? $recipients;
            // 3. Jika nomor ADA, lanjut ke proses kirim WA
            try {
                // Berikan jeda agar API tidak menganggap spam
                sleep(1);

                $url_send = $this->baseUrl . 'send/message';

                $sendResponse = Http::withBasicAuth($this->BasicUser, $this->BasicPasswd)
                    ->withHeaders([
                        'Accept'      => 'application/json',
                        'X-Device-Id' => $this->DeviceID,
                    ])
                    ->post($url_send, [
                        'phone'        => $formattedPhone . '@s.whatsapp.net',
                        'message'      => $message,
                        'is_forwarded' => false,
                        'duration'     => 0,
                    ]);

                $sendResponse->throw();

                return response()->json([
                    'status'  => true,
                    'message' => 'Pesan berhasil dikirim.',
                    'data'    => $sendResponse->json()
                ]);

            } catch (\Throwable $e) {
                $errorMessage = $e->getMessage();
                if ($e instanceof \Illuminate\Http\Client\RequestException) {
                    $errorMessage = $e->response->body();
                }
                Log::error('WhatsApp Service Error [sendMessage]: ' . $errorMessage);

                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal mengirim pesan WhatsApp.',
                    'error'   => $errorMessage
                ], 500);
            }
        }
        public function sendLink($recipients, $link, $caption)
        {
            // 1. Panggil fungsi cek nomor yang baru saja kita buat
            $check = $this->checkNumber($recipients);

            // 2. Jika nomor TIDAK ADA di WA (status = false), langsung hentikan dan kembalikan error
            if ($check['status'] === false) {
                return response()->json([
                    'status'  => false,
                    'message' => $check['message']
                ]);
            }
            $formattedPhone = $check['formatted_phone'] ?? $recipients;
            try {
                // Delay 1 detik
                sleep(1);
                $url_send = $this->baseUrl . 'send/link';

                $sendResponse = Http::withBasicAuth($this->BasicUser, $this->BasicPasswd)
                    ->withHeaders([
                        'Accept'      => 'application/json',
                        'X-Device-Id' => $this->DeviceID, // Diperbaiki & disamakan
                    ])
                    ->post($url_send, [
                        'phone'        => $formattedPhone . '@s.whatsapp.net',
                        'link'         => $link, // Pastikan controller mengirim link berawalan http:// atau https://
                        'caption'      => $caption,
                        'is_forwarded' => false,
                        'duration'     => 0,
                    ]);

                // Lempar error jika gagal kirim
                $sendResponse->throw();

                return response()->json([
                    'status'  => true,
                    'message' => 'Link berhasil dikirim.',
                    'data'    => $sendResponse->json()
                ]);

            } catch (\Throwable $e) {
                $errorMessage = $e->getMessage();
                if ($e instanceof \Illuminate\Http\Client\RequestException) {
                    $errorMessage = $e->response->body();
                }
                Log::error('WhatsApp Service Error [sendLink]: ' . $errorMessage);

                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal mengirim pesan WhatsApp.',
                    'error'   => $errorMessage
                ], 500);
            }
        }

    }
