<?php

namespace App\Mail;

use App\Models\AgencyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgencyNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The agency notification being sent.
     */
    public AgencyNotification $notification;

    /**
     * Create a new message instance.
     *
     * @param  AgencyNotification  $notification
     */
    public function __construct(AgencyNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notification->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.agency_notification_email',
            with: [
                'notification' => $this->notification,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
