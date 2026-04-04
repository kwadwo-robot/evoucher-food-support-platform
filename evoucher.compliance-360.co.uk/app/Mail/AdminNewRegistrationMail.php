<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User   $user;
    public string $roleLabel;

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
        $this->user      = $user;
        $this->roleLabel = self::$roleLabels[$user->role] ?? ucfirst($user->role);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Admin] New Registration – ' . $this->roleLabel . ' awaiting approval',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-registration',
            with: [
                'userName'     => $this->user->name,
                'userEmail'    => $this->user->email,
                'roleLabel'    => $this->roleLabel,
                'registeredAt' => $this->user->created_at->format('d M Y, H:i') . ' UTC',
                'adminUrl'     => url('/admin/users'),
            ],
        );
    }
}
