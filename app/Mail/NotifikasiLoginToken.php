<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Dokumen;
use App\Models\Unit;
use App\Models\User;

class NotifikasiLoginToken extends Mailable
{
    use Queueable, SerializesModels;

    public $dokumen;
    public $unit;
    public $user;
    public $token;
    public $tipe;

    public function __construct(Dokumen $dokumen, Unit $unit, User $user, $token, $tipe)
    {
        $this->dokumen = $dokumen;
        $this->unit = $unit;
        $this->user = $user;
        $this->token = $token;
        $this->tipe = $tipe;
    }

    public function build()
    {
        $subject = 'Laporan Baru Memerlukan Persetujuan: ' . $this->dokumen->nama_dokumen;
        
        return $this->subject($subject)
                    ->view('emails.login-token');
    }
}

