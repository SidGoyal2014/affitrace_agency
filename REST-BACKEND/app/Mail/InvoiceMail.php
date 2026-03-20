<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoiceData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $invoiceData)
    {
        $this->invoiceData = $invoiceData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->invoiceData['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice_email',
            with: [
                'invoiceData' => $this->invoiceData,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!empty($this->invoiceData['pdf_path']) && file_exists($this->invoiceData['pdf_path'])) {
            return [
                Attachment::fromPath($this->invoiceData['pdf_path'])
                    ->as($this->invoiceData['pdf_filename'] ?? 'invoice.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
