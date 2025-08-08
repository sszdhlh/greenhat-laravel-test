<?php

namespace Modules\Employee\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Employee\Mail\EmployeeRegistrationConfirmation;
use Modules\Employee\Models\Employee;

class SendEmployeeRegistrationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Employee $employee
    ) {}

    public function handle(): void
    {
        Log::info('SendEmployeeRegistrationEmail started for employee ID: ' . $this->employee->id);
        
        try {
            // Send the confirmation email
            Mail::to($this->employee->user->email)
                ->send(new EmployeeRegistrationConfirmation($this->employee));
            
            Log::info('Email sent successfully to: ' . $this->employee->user->email);
            
            // Update the confirmation_email_sent_at timestamp
            $updated = $this->employee->update([
                'confirmation_email_sent_at' => now(),
            ]);
            
            Log::info('Database update result: ' . ($updated ? 'success' : 'failed'));
            Log::info('Updated timestamp: ' . $this->employee->fresh()->confirmation_email_sent_at);
            
        } catch (\Exception $e) {
            Log::error('Error in SendEmployeeRegistrationEmail: ' . $e->getMessage());
            throw $e;
        }
    }
}
