<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffRegistrationInvite extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;
    public string $role;

    public function __construct(string $token, string $role)
    {
        $this->token = $token;
        $this->role = $role;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're Invited to Join Vuka Shop as " . ucfirst($this->role),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staff-invite',
            with: [
                'token' => $this->token,
                'role' => $this->role,
                'registerUrl' => route('staff.register.form', ['token' => $this->token]),
            ]
        );
    }
}
