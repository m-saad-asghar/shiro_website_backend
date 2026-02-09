<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WebsiteNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function build()
    {
        $subject = 'New Website Notification';

        // Optional: if your payload has a title like "Website Lead"
        if (!empty($this->payload['title_to_api'])) {
            $subject = $this->payload['title_to_api'] . ' - New Notification';
        }

        $mail = $this->subject($subject)
            ->view('emails.website-notification')
            ->with([
                'payload' => $this->payload,
            ]);

        // Optional Reply-To (if user email exists)
        if (!empty($this->payload['email'])) {
            $mail->replyTo($this->payload['email']);
        }

        return $mail;
    }
}