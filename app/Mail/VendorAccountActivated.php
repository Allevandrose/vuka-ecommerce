<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorAccountActivated extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Vuka Shop Vendor Account Is Now Active',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-account-activated',
            with: [
                'user' => $this->user,
                'loginUrl' => route('login'),
                'dashboardUrl' => route('vendor.dashboard'),
            ],
        );
    }
}
