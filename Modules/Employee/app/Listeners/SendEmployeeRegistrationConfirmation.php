<?php

namespace Modules\Employee\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Employee\Events\EmployeeRegistered;
use Modules\Employee\Emails\EmployeeRegistrationConfirmation;

class SendEmployeeRegistrationConfirmation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(EmployeeRegistered $event): void
    {
        // Send the confirmation email
        Mail::to($event->employee->user->email)
            ->send(new EmployeeRegistrationConfirmation($event->employee));

        // Update the confirmation_email_sent_at field
        $event->employee->update([
            'confirmation_email_sent_at' => now()
        ]);
    }
}
