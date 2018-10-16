<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $confirm_code;
    protected $user_info;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code, $user)
    {
        $this->confirm_code = $code;
        $this->user_info = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $link = env('APP_URL').'/api/user/verifyMail/'.$this->confirm_code;
        return $this->subject('Aktiviere deinen InGroups-Account')
            ->view('emails.emailverification')->with([
                'confirm_link' => $link,
                'user_info' => $this->user_info
            ]);
    }
}
