<?php
namespace App\Mail;
use App\Models\Broadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class BroadcastMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $broadcast;
    protected $recipientUser;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Broadcast $broadcast, $recipientUser)
    {
        $this->broadcast = $broadcast;
        $this->recipientUser = $recipientUser;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Handle both User object and string (recipient name)
        if (is_string($this->recipientUser)) {
            $recipientName = $this->recipientUser;
        } else {
            $recipientName = $this->recipientUser->name ?? 'Recipient';
        }
        
        return $this->subject($this->broadcast->title)
                    ->view('emails.broadcast')
                    ->with([
                        'broadcast' => $this->broadcast,
                        'recipientName' => $recipientName,
                    ]);
    }
}
