<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $post;
    public $user;
    public function __construct($post, $user)
    {
        $this->post = $post;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject("Cập nhật trạng thái bài viết: {$this->post->title}")
            ->view('emails.post_status_changed')
            ->with([
                'post' => $this->post,
                'user' => $this->user
            ]);
    }
    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Post Status Changed',
    //     );
    // }

    // /**
    //  * Get the message content definition.
    //  */
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
