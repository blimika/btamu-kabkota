<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KirimAntrian extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $body;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($body)
    {
        //
        $this->body = $body;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@statsntb.id',ENV('NAMA_APLIKASI'))
                    ->subject('[NOREPLY] Nomor Antrian')
                    ->markdown('emails.newantrian')->with('body',$this->body);
    }
}
