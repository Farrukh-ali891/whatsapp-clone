<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailSender extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $mail_content;
    public string $mail_view;

    public function __construct(array $content, string $view)
    {
        $this->mail_content = $content;
        $this->mail_view = $view;
    }

    public function build()
    {
        $subject = !empty($this->mail_content['subject']) ? $this->mail_content['subject'] : '';

        return $this->subject($subject)
            ->view('mails.' . $this->mail_view)
            ->with(['data' => $this->mail_content]);
    }
}
