<?php

namespace App\Mail;

use App\Models\User;
use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public VendorApplication $application;
    public User $admin;

    public function __construct(VendorApplication $application, User $admin)
    {
        $this->application = $application;
        $this->admin = $admin;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Vendor Application: ' . $this->application->shop_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-application-received',
            with: [
                'application' => $this->application,
                'admin' => $this->admin,
                'reviewUrl' => route('admin.vendors.show', $this->application),
            ],
        );
    }
}
