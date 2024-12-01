<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\Person\PhoneNum;
use App\Models\Esims\ClientEsim;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyClientEsimProfile extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $client;
    public $phonenum;

    /**
     * Create a new message instance.
     */
    public function __construct(ClientEsim $client, PhoneNum $phonenum)
    {
        $this->client = $client;

        $phonenum->load('esim');
        $this->phonenum = $phonenum;

        $this->subject = "Fiche de Configuration de Profile e-sim";
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'clientesims.emailprofile',
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
