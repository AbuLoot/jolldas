<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Track;

class SendMailNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $track;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Track $track, $user)
    {
        $this->track = $track;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('joldas7799@gmail.com', 'Jolldas Service'),
            subject: 'Поступили новые грузы на склад',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.arrived-tracks',
            with: [
                'trackCode' => $this->track->code,
                'trackUser' => $this->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
