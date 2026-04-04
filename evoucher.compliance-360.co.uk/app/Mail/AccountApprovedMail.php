<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User   $user;
    public string $roleLabel;
    public string $loginUrl;
    public string $supportEmail;

    private static array $roleLabels = [
        'recipient'   => 'Recipient',
        'shop'        => 'Local Food Shop',
        'vcfse'       => 'VCFSE Organisation',
        'school_care' => 'School / Care Organisation',
        'admin'       => 'Administrator',
        'super_admin' => 'Super Administrator',
    ];

    public function __construct(User $user)
    {
        $this->user         = $user;
        $this->roleLabel    = self::$roleLabels[$user->role] ?? ucfirst($user->role);
        $this->loginUrl     = url('/login');
        $this->supportEmail = Setting::get('support_email', config('mail.from.address', 'support@evoucher.org'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your eVoucher Account Has Been Approved!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-approved',
            with: [
                'userName'     => $this->user->name,
                'roleLabel'    => $this->roleLabel,
                'loginUrl'     => $this->loginUrl,
                'supportEmail' => $this->supportEmail,
            ],
        );
    }
}
