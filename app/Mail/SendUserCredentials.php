<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SendUserCredentials extends Mailable
{
    public $username;
    public $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('بيانات حسابك')
                    ->view('emails.user_credentials');
    }
}