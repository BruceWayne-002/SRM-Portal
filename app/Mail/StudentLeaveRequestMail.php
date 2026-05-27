<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentLeaveRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $student;
    public $senderEmail;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\StudentLeave  $leave
     * @param  \App\Models\Student       $student
     * @param  string|null               $senderEmail
     */
    public function __construct($leave, $student, $senderEmail = null)
    {
        $this->leave = $leave;
        $this->student = $student;
        $this->senderEmail = $senderEmail ?? $student->email; // fallback to student email
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Leave Request from ' . $this->student->name)
                    ->from($this->senderEmail, $this->student->name)
                    ->view('emails.student_leave_request');
    }
}
