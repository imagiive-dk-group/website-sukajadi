<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Message $contactMessage)
    {
        $this->locale('id');
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('site.mail.message_subject', ['name' => $this->contactMessage->name]));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.message-received');
    }
}
