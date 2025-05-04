<?php

namespace App\Mail;

use App\Models\Dokumen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DokumenNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Dokumen yang akan dikirimkan melalui email.
     *
     * @var \App\Models\Dokumen
     */
    public $dokumen;
    
    /**
     * Subjek email.
     *
     * @var string
     */
    public $emailSubject;
    
    /**
     * Pesan email.
     *
     * @var string
     */
    public $emailMessage;
    
    /**
     * Data login (opsional).
     *
     * @var array|null
     */
    public $loginData;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Dokumen $dokumen
     * @param string $subject
     * @param string $message
     * @param array|null $loginData
     * @return void
     */
    public function __construct(Dokumen $dokumen, $subject, $message, $loginData = null)
    {
        $this->dokumen = $dokumen;
        $this->emailSubject = $subject;
        $this->emailMessage = $message;
        $this->loginData = $loginData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailSubject)
                    ->view('emails.dokumen-notification');
    }
}