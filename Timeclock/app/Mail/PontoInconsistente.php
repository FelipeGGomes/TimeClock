<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PontoInconsistente extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $date;
    public $missingTypes;

    public function __construct($user, $date, $missingTypes)
    {
        $this->user = $user;
        $this->date = $date;
        $this->missingTypes = $missingTypes;
    }

    public function build(){
        return $this->subject('Aviso de Ponto')
                    ->view('emails.ponto_inconsistente');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ponto Inconsistente',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ponto_inconsistente',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
