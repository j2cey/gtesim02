<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserAccountInfos extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $user;
    public $login;
    public $pwd;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $pwd = null)
    {
        $this->user = $user;
        $this->subject = "Votre compte d'accès à GT-E-Sim";
        $this->login = $user->is_ldap ? $user->login : $user->email;
        $this->pwd = $user->is_ldap ? 'Celui utilisé pour ouvrir votre session Windows' : ( is_null($pwd) ? '' : $pwd );
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
            view: 'users.emailaccountinfos',
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
