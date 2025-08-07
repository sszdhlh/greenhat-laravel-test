<?php

namespace Modules\Employee\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Employee\Models\Employee;

class EmployeeRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Employee $employee
    ) {}

    public function build(): self
    {
        return $this
            ->to($this->employee->user->email, $this->employee->user->name)
            ->view('employee::emails.registration-confirmation')
            ->subject('Welcome to ACME - Registration Confirmed')
            ->with([
                'employee' => $this->employee,
                'registrationDate' => $this->employee->created_at?->toDateString(),
                'userName' => $this->employee->user->name,
                'userEmail' => $this->employee->user->email,
            ]);
    }
}
