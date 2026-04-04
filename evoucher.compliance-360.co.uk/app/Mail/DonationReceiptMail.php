<?php

namespace App\Mail;

use App\Models\Donation;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public Donation $donation;
    public string   $supportEmail;

    public function __construct(Donation $donation)
    {
        $this->donation     = $donation;
        $this->supportEmail = Setting::get('support_email', config('mail.from.address', 'support@evoucher.org'));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You for Your Donation – eVoucher Food Support',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.donation-receipt',
            with: [
                'donorEmail'   => $this->donation->donor_email,
                'amount'       => $this->donation->amount,
                'currency'     => $this->donation->currency ?? 'GBP',
                'paymentId'    => $this->donation->stripe_payment_id ?? 'N/A',
                'donatedAt'    => $this->donation->created_at->format('d M Y, H:i') . ' UTC',
                'supportEmail' => $this->supportEmail,
            ],
        );
    }
}
