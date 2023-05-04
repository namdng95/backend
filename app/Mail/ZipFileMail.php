<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class ZipFileMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Data
     *
     * @var array
     */
    protected array $data;

    /**
     * Create a new message instance.
     *
     * @param array $data Data
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        $title = $this->data['title'] ?? 'Congratulation!!!';

        return new Envelope(
            subject: $title,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        $content = $this->data['body'];

        return new Content(
            view: 'emails.send_mail',
            with: [
                'body' => $content,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        $zipName = $this->data['zip_name'];
        $filePath = $this->data['file_path'];

        return [
            Attachment::fromStorageDisk('local', $zipName),
            Attachment::fromStorageDisk('s3', $filePath)
//                ->as("avatar.pdf")
//                ->withMime('application/pdf'),
                ->as("Logo User")
                ->withMime('image/jpeg'),
        ];
    }
}
