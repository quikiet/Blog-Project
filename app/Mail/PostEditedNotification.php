<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostEditedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $author;
    public $changedDetails;
    public function __construct($post, $author, $changedDetails)
    {
        $this->post = $post;
        $this->author = $author;
        $this->changedDetails = $changedDetails;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo bài báo được cập nhật',
        );
    }

    public function build()
    {
        return $this->subject('Bài viết đã được chỉnh sửa')
            ->view('emails.post_edited_notification');
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array<int, \Illuminate\Mail\Mailables\Attachment>
    //  */
    // public function attachments(): array
    // {
    //     return [];
    // }
}
