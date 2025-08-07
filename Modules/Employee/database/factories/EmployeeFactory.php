<?php

namespace Modules\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'role' => fake()->randomElement([
                'Software Developer',
                'Senior Developer',
                'Team Lead',
                'Project Manager',
                'Designer',
                'QA Engineer',
                'DevOps Engineer'
            ]),
            'highest_qualification' => fake()->randomElement([
                'Bachelor\'s Degree in Computer Science',
                'Master\'s Degree in Software Engineering',
                'PhD in Computer Science',
                'Bootcamp Graduate',
                'Self-taught'
            ]),
            'desired_salary' => fake()->numberBetween(40000, 150000),
            'note' => fake()->optional()->sentence(),
        ];
    }

    public function withUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function developer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'Software Developer',
            'highest_qualification' => 'Bachelor\'s Degree in Computer Science',
            'desired_salary' => fake()->numberBetween(60000, 100000),
        ]);
    }

    public function senior(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'Senior Developer',
            'highest_qualification' => 'Master\'s Degree in Software Engineering',
            'desired_salary' => fake()->numberBetween(90000, 150000),
        ]);
    }
}
