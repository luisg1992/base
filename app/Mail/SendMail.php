<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

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
        $mail = $this->subject($this->data['subject'])
            ->from($this->data['from_address'], $this->data['from_name'])
            ->view($this->data['template']);

        if($this->data['attach']) {
            $mail->attachData(base64_decode($this->data['file_base64']), $this->data['filename_original']);
        }

        return $mail;
    }
}
