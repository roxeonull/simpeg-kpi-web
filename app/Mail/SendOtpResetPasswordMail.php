<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $userName;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $userName)
    {
        $this->otp = $otp;
        $this->userName = $userName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Reset Password SIMPEG KPI',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp_reset_password',
            with: [
                'otp' => $this->otp,
                'userName' => $this->userName,
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
