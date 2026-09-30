<?php

namespace App\Mail;

use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorRegistrationInvite extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;
    public VendorApplication $application;

    public function __construct(string $token, VendorApplication $application)
    {
        $this->token = $token;
        $this->application = $application;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Vuka Shop Vendor Application Has Been Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-invite',
            with: [
                'token' => $this->token,
                'application' => $this->application,
                'registerUrl' => route('vendor.register.form', ['token' => $this->token]),
            ],
        );
    }
}
