<?php

namespace Modules\Course\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Course\Models\Course;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'instructor' => $this->faker->name(),
            'duration_hours' => $this->faker->numberBetween(10, 100),
            'max_participants' => $this->faker->numberBetween(10, 50),
            'status' => $this->faker->randomElement([
                Course::STATUS_DRAFT,
                Course::STATUS_PUBLISHED,
                Course::STATUS_COMPLETED,
                Course::STATUS_CANCELLED
            ]),
            'start_date' => $this->faker->optional()->dateTimeBetween('now', '+6 months'),
            'end_date' => $this->faker->optional()->dateTimeBetween('+1 month', '+12 months'),
            'price' => $this->faker->optional()->randomFloat(2, 50, 1000),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Course::STATUS_PUBLISHED,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Course::STATUS_DRAFT,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
