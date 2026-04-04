<?php
namespace App\Mail;
use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class VoucherIssuedMail extends Mailable
{
    use Queueable, SerializesModels;
    public Voucher $voucher;
    public string  $recipientName;
    public string  $issuedByName;
    public string  $dashboardUrl;
    public string  $supportEmail;
    public string  $qrCodeBase64;
    public ?string $tempPassword;
    public ?string $recipientEmail;
    public function __construct(Voucher $voucher, ?string $tempPassword = null)
    {
        $this->voucher       = $voucher;
        $this->tempPassword  = $tempPassword;
        $this->recipientName = $voucher->recipient->name ?? 'Valued Recipient';
        $this->recipientEmail = $voucher->recipient->email ?? '';
        $this->issuedByName  = $voucher->issuedBy->name ?? 'eVoucher Admin';
        $this->dashboardUrl  = url('/recipient/vouchers');
        $this->supportEmail  = Setting::get('support_email', config('mail.from.address', 'support@evoucher.org'));
        
        // Generate QR code as base64
        try {
            $qrCode = QrCode::format('png')
                ->size(200)
                ->generate($voucher->code);
            $this->qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCode);
        } catch (\Exception $e) {
            $this->qrCodeBase64 = '';
        }
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Food Support Voucher – eVoucher Food Support',
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'emails.voucher-issued',
            with: [
                'voucher'       => $this->voucher,
                'recipientName' => $this->recipientName,
                'recipientEmail' => $this->recipientEmail,
                'issuedByName'  => $this->issuedByName,
                'dashboardUrl'  => $this->dashboardUrl,
                'supportEmail'  => $this->supportEmail,
                'qrCodeBase64'  => $this->qrCodeBase64,
                'tempPassword'  => $this->tempPassword,
            ],
        );
    }
}
