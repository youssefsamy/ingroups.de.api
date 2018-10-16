<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $confirm_code;
    protected $contact_info;
    protected $user_info;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code, $user, $contact_info)
    {
        $this->confirm_code = $code;
        $this->user_info = $user;
        $this->contact_info = $contact_info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //$link = env('APP_URL').'/api/event/verify_new_event/'.$this->confirm_code;
        return $this->subject('Neue Nachricht zu deinem Event')
            ->view('emails.contact')->with([
                //'confirm_link' => $link,
                'user_info' => $this->user_info,
                'contact_info' => $this->contact_info
            ]);
    }
}
