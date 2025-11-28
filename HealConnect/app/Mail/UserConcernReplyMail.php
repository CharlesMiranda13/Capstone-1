<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactMessage;

class UserConcernReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageData;
    public $reply;

    public function __construct(ContactMessage $messageData, $reply)
    {
        $this->messageData = $messageData;
        $this->reply = $reply;
    }

    public function build()
    {
        return $this->subject('Reply to your concern')
                    ->view('email.reply_to_concerns')
                    ->with([
                        'name' => $this->messageData->name,
                        'originalMessage' => $this->messageData->message,
                        'reply' => $this->reply,
                    ]);
    }
}
