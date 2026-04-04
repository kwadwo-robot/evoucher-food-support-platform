<?php

namespace App\Mail;

use App\Models\Donation;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewDonationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Donation $donation;

    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Admin] New Donation Received – £' . number_format($this->donation->amount, 2),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-donation',
            with: [
                'donorEmail' => $this->donation->donor_email,
                'amount'     => $this->donation->amount,
                'paymentId'  => $this->donation->stripe_payment_id ?? 'N/A',
                'donatedAt'  => $this->donation->created_at->format('d M Y, H:i') . ' UTC',
                'adminUrl'   => url('/admin/donations'),
            ],
        );
    }
}
