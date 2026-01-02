<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MovieInfoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $movie;
    public $customMessage;

    public function __construct($movie, $customMessage, $subject)
    {
        $this->movie = $movie;
        $this->customMessage = $customMessage;
        $this->subject = $subject;
    }

    public function build()
    {
        return $this->view('emails.movie-info')
            ->subject($this->subject);
    }
}
