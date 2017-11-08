<?php

namespace App\Mail;

use Config;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from_email = Config::get('mail.from.address');
        
        return $this->from($from_email)
        ->subject('Please reset your password')
        ->view('email_templates.forgot_password')
        ->with([
            'user_slack' => $this->data['user_slack'],
            'password_reset_token' => $this->data['password_reset_token'],
        ]);
    }
}
