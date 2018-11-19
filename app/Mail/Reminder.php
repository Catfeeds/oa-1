<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;

class Reminder extends Mailable
{
    use Queueable, SerializesModels;

    protected $exception;

    protected $url;

    protected $user;

    public function __construct(\Exception $exception, $url, $user)
    {
        $this->exception = FlattenException::create($exception);
        $this->url = $url;
        $this->user = $user;
    }

    public function build()
    {
        $h = new SymfonyExceptionHandler();
        return $this->subject($this->exception->getMessage())->view('emails.reminder', [
            'content' => $h->getContent($this->exception),
            'url' => $this->url,
            'user' => $this->user,
        ]);
    }
}
