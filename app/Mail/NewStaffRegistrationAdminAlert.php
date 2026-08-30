<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewStaffRegistrationAdminAlert extends Mailable
{
    use Queueable, SerializesModels;

    public User $newUser;
    public User $admin;

    public function __construct(User $newUser, User $admin)
    {
        $this->newUser = $newUser;
        $this->admin = $admin;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Staff Registration Needs Your Approval',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-staff-registration-admin',
            with: [
                'newUser' => $this->newUser,
                'admin' => $this->admin,
                'manageUrl' => route('admin.staff.index'),
            ]
        );
    }
}
