<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;


    public $resetPasswordUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($resetPasswordUrl)
    {
        $this->resetPasswordUrl = $resetPasswordUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reset_password')
                    ->with(['resetPasswordUrl' => $this->resetPasswordUrl])
                    ->subject('Reset Password');
    }
}
