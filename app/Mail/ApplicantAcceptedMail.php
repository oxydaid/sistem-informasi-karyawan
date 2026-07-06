<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;

    public $onboardingUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Applicant $applicant, string $onboardingUrl)
    {
        $this->applicant = $applicant;
        $this->onboardingUrl = $onboardingUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat! Lamaran Anda Diterima - ISP HRIS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.applicant_accepted',
        );
    }
}
