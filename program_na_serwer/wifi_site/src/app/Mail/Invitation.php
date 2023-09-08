<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Invitation extends Mailable
{
    use Queueable, SerializesModels;

    public $name = '';
    public $newUser = '';
    public $password = '';

    /**
     * Create a new message instance.
     * @param $name
     */
    public function __construct($name, $newUser = false, $password = '')
    {
        $this->name = $name;
        $this->newUser = $newUser;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.invitation',
            [
                'greeting' => 'Hello '.$this->name.'!',
                'introLines' => array('You are receiving this email because somebody invited you to manage his devices.'),
                'actionText' => 'Go to portal',
                'actionUrl' => url('login'),
                //'outroLines' => array('If you don`t have account please register first.'),
                'newUser' => $this->newUser,
                'password' => $this->password
            ]);
    }
}
