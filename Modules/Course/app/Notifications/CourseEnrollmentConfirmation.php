<?php

namespace Modules\Course\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Course\Models\Course;
use Modules\Employee\Models\Employee;

class CourseEnrollmentConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Course $course,
        public Employee $employee
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Course Enrollment Confirmation - ' . $this->course->title)
            ->greeting('Hello ' . $this->employee->user->first_name . '!')
            ->line('You have successfully enrolled in the course: ' . $this->course->title)
            ->line('Course Duration: ' . $this->course->duration_hours . ' hours')
            ->when($this->course->start_date, function ($mail) {
                return $mail->line('Start Date: ' . $this->course->start_date->format('M d, Y'));
            })
            ->when($this->course->instructor, function ($mail) {
                return $mail->line('Instructor: ' . $this->course->instructor);
            })
            ->line('Thank you for your interest in professional development!')
            ->salutation('Best regards, The Learning Team');
    }
}
