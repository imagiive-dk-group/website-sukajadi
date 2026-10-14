<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email booking baru. $forAdmin = true untuk notifikasi pengelola,
 * false untuk konfirmasi ke pemesan (mengikuti bahasa pemesan).
 */
class BookingReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public bool $forAdmin = false)
    {
        $this->locale($forAdmin ? 'id' : $booking->locale);
    }

    public function envelope(): Envelope
    {
        $key = $this->forAdmin ? 'site.mail.admin_subject' : 'site.mail.customer_subject';

        return new Envelope(
            subject: __($key, ['code' => $this->booking->code, 'package' => $this->booking->package_name]),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.booking-received');
    }
}
