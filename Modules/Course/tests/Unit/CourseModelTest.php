<?php

namespace Modules\Course\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Employee\Models\Employee;
use Tests\TestCase;

class CourseModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_many_categories()
    {
        // Arrange
        $course = Course::factory()->create();
        $categories = CourseCategory::factory()->count(2)->create();

        // Act
        $course->categories()->attach($categories->pluck('id'));

        // Assert
        $this->assertCount(2, $course->categories);
        $this->assertInstanceOf(CourseCategory::class, $course->categories->first());
    }

    /** @test */
    public function it_has_many_enrolled_employees()
    {
        // Arrange
        $course = Course::factory()->create();
        $employees = Employee::factory()->count(3)->create();

        // Act
        $course->enrolledEmployees()->attach($employees->pluck('id'), [
            'enrolled_at' => now(),
            'status' => 'enrolled'
        ]);

        // Assert
        $this->assertCount(3, $course->enrolledEmployees);
        $this->assertInstanceOf(Employee::class, $course->enrolledEmployees->first());
    }

    /** @test */
    public function it_can_get_status_options()
    {
        // Act
        $statusOptions = Course::getStatusOptions();

        // Assert
        $this->assertIsArray($statusOptions);
        $this->assertArrayHasKey(Course::STATUS_DRAFT, $statusOptions);
        $this->assertArrayHasKey(Course::STATUS_PUBLISHED, $statusOptions);
        $this->assertArrayHasKey(Course::STATUS_COMPLETED, $statusOptions);
        $this->assertArrayHasKey(Course::STATUS_CANCELLED, $statusOptions);
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        // Arrange
        $courseData = [
            'title' => 'Test Course',
            'description' => 'Test Description',
            'instructor' => 'Test Instructor',
            'duration_hours' => '40',
            'max_participants' => '30',
            'price' => '299.99',
            'is_active' => '1',
            'start_date' => '2025-01-01',
            'end_date' => '2025-02-01'
        ];

        // Act
        $course = Course::create($courseData);

        // Assert
        $this->assertIsInt($course->duration_hours);
        $this->assertIsInt($course->max_participants);
        $this->assertIsFloat($course->price);
        $this->assertIsBool($course->is_active);
        $this->assertInstanceOf(\Carbon\Carbon::class, $course->start_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $course->end_date);
    }
}
