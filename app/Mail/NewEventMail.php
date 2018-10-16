<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewEventMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $confirm_code;
    protected $event_info;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code, $info)
    {
        $this->confirm_code = $code;
        $this->event_info = $info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $link = env('APP_URL').'/api/event/verify_new_event/'.$this->confirm_code;
        return $this->subject('Dein Event')
            ->view('emails.newevent')->with([
                //'confirm_link' => $link
                'event_info' => $this->event_info
            ]);
    }
}
