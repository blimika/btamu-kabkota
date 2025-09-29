<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use App\Whatsapp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    public function index()
    {
        $data = Whatsapp::orderBy('created_at','desc')->take(100)->get();
        return view('whatsapp.index',[
                    'data'=>$data
                ]);
    }
}
