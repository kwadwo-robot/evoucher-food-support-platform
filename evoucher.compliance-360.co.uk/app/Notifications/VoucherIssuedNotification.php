<?php

namespace App\Notifications;

use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VoucherIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $voucher;

    /**
     * Create a new notification instance.
     */
    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Voucher Issued - eVoucher Platform')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new voucher has been issued to you.')
            ->line('**Voucher Code:** ' . $this->voucher->code)
            ->line('**Voucher Value:** £' . number_format($this->voucher->value, 2))
            ->line('**Expiry Date:** ' . $this->voucher->expiry_date->format('d M Y'))
            ->when($this->voucher->notes, function ($mail) {
                return $mail->line('**Notes:** ' . $this->voucher->notes);
            })
            ->action('View Voucher', url('/recipient/vouchers/' . $this->voucher->id))
            ->line('You can use this voucher to purchase food items from participating shops.')
            ->line('Thank you for using eVoucher!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'voucher_id' => $this->voucher->id,
            'voucher_code' => $this->voucher->code,
            'voucher_value' => $this->voucher->value,
            'expiry_date' => $this->voucher->expiry_date,
            'message' => 'You have received a new voucher worth £' . number_format($this->voucher->value, 2),
        ];
    }
}
