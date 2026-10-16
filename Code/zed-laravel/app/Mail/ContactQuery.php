<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Website Contact Us query, e-mailed to the ZED team (reply goes to the sender). */
class ContactQuery extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $contact) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contact Us: '.$this->contact->query_category,
            replyTo: [new Address($this->contact->contact_email, $this->contact->contact_name)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.contact-query');
    }
}
