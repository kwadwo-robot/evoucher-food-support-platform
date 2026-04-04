<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User   $user;
    public string $changedAt;
    public string $loginUrl;
    public string $supportEmail;

    public function __construct(User $user)
    {
        $this->user         = $user;
        $this->changedAt    = now()->format('d M Y, H:i') . ' UTC';
        $this->loginUrl     = url('/login');
        $this->supportEmail = Setting::get('support_email', config('mail.from.address', 'support@evoucher.org'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your eVoucher Password Has Been Changed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-changed',
            with: [
                'userName'     => $this->user->name,
                'userEmail'    => $this->user->email,
                'changedAt'    => $this->changedAt,
                'loginUrl'     => $this->loginUrl,
                'supportEmail' => $this->supportEmail,
            ],
        );
    }
}
