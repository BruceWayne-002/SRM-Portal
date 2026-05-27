<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $teacher;
    public $senderEmail;

    public function __construct($leave, $teacher, $senderEmail)
    {
        $this->leave = $leave;
        $this->teacher = $teacher;
        $this->senderEmail = $senderEmail;
    }

    public function build()
    {
        return $this->subject('New Leave Request from ' . $this->teacher->name)
                    ->from($this->senderEmail, $this->teacher->name)
                    ->view('emails.teacher_mail');
    }
}
