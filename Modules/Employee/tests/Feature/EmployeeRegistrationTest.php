<?php

namespace Modules\Employee\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\Employee\Mail\EmployeeRegistrationConfirmation;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;
use Tests\TestCase;

class EmployeeRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_register_with_valid_data()
    {
        Mail::fake();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'role' => 'Software Developer',
            'highest_qualification' => 'Bachelor\'s Degree',
            'desired_salary' => 75000,
            'note' => 'Looking forward to joining the team',
            'code' => 'ACME',
        ];

        $response = $this->postJson('/v1/employee-registration', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Employee registered successfully',
            ]);

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'name' => 'John Doe',
        ]);

        // Assert employee was created
        $this->assertDatabaseHas('employees', [
            'role' => 'Software Developer',
            'highest_qualification' => 'Bachelor\'s Degree',
            'desired_salary' => 75000,
            'note' => 'Looking forward to joining the team',
        ]);

        // Assert email was sent
        Mail::assertSent(EmployeeRegistrationConfirmation::class);
    }

    public function test_employee_registration_fails_with_invalid_code()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'role' => 'Software Developer',
            'code' => 'INVALID',
        ];

        $response = $this->postJson('/v1/employee-registration', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('code');
    }

    public function test_employee_registration_fails_with_duplicate_email()
    {
        // Create existing user
        User::factory()->create(['email' => 'john.doe@example.com']);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'role' => 'Software Developer',
            'code' => 'ACME',
        ];

        $response = $this->postJson('/v1/employee-registration', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }
}
