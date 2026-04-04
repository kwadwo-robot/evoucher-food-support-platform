<?php

namespace App\Mail;

use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VoucherIssuedConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Voucher $voucher;
    public string  $issuerName;
    public string  $recipientName;
    public string  $dashboardUrl;
    public string  $supportEmail;

    public function __construct(Voucher $voucher)
    {
        $issuer = $voucher->issuedBy;
        $recipient = $voucher->recipient;

        $this->voucher       = $voucher;
        $this->issuerName    = $issuer->name ?? 'Organisation';
        $this->recipientName = $recipient->name ?? 'Recipient';
        $this->supportEmail  = Setting::get('support_email', config('mail.from.address', 'support@evoucher.org'));

        // Route to the correct dashboard based on issuer role
        $this->dashboardUrl = match ($issuer->role ?? 'admin') {
            'vcfse'       => url('/vcfse/vouchers'),
            'school_care' => url('/school/vouchers'),
            default       => url('/admin/vouchers'),
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Voucher Issued – ' . $this->voucher->code . ' for ' . $this->recipientName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.voucher-issued-confirmation',
            with: [
                'voucher'       => $this->voucher,
                'issuerName'    => $this->issuerName,
                'recipientName' => $this->recipientName,
                'dashboardUrl'  => $this->dashboardUrl,
                'supportEmail'  => $this->supportEmail,
            ],
        );
    }
}
