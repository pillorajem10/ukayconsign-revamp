<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailBlastMail extends Mailable
{
    use SerializesModels;

    public $subject;
    public $body;
    public $storeName;

    public function __construct($subject, $body, $storeName)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->storeName = $storeName;
    }

    public function build()
    {
        return $this->from('uscsupport@ukayukaysupplier.com', $this->storeName)
                    ->subject($this->subject)
                    ->view('emails.emailBlast')
                    ->with([
                        'body' => $this->body,
                    ]);
    }
}

