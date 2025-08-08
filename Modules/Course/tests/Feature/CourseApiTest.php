<?php

namespace Modules\Course\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Enable Course module for testing
        $this->artisan('module:enable Course');
    }

    /** @test */
    public function it_can_list_courses()
    {
        // Arrange
        Course::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/v1/courses');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'instructor',
                        'duration_hours',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page'
                ]
            ]);
    }

    /** @test */
    public function it_can_create_a_course()
    {
        // Arrange
        $category = CourseCategory::factory()->create();
        $courseData = [
            'title' => 'Laravel Advanced Techniques',
            'description' => 'Advanced Laravel development techniques and best practices',
            'instructor' => 'Jane Smith',
            'duration_hours' => 60,
            'max_participants' => 25,
            'status' => 'published',
            'price' => 399.99,
            'category_ids' => [$category->id]
        ];

        // Act
        $response = $this->postJson('/v1/courses', $courseData);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Course created successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'instructor',
                    'duration_hours',
                    'max_participants',
                    'status',
                    'price',
                    'categories_count'
                ]
            ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Laravel Advanced Techniques',
            'instructor' => 'Jane Smith'
        ]);
    }

    /** @test */
    public function it_can_show_a_single_course()
    {
        // Arrange
        $course = Course::factory()->create();

        // Act
        $response = $this->getJson("/v1/courses/{$course->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $course->id,
                    'title' => $course->title
                ]
            ]);
    }

    /** @test */
    public function it_can_update_a_course()
    {
        // Arrange
        $course = Course::factory()->create();
        $updateData = [
            'title' => 'Updated Course Title',
            'instructor' => 'Updated Instructor'
        ];

        // Act
        $response = $this->putJson("/v1/courses/{$course->id}", $updateData);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Course updated successfully'
            ]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Updated Course Title',
            'instructor' => 'Updated Instructor'
        ]);
    }

    /** @test */
    public function it_can_delete_a_course()
    {
        // Arrange
        $course = Course::factory()->create();

        // Act
        $response = $this->deleteJson("/v1/courses/{$course->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Course deleted successfully'
            ]);

        $this->assertSoftDeleted('courses', [
            'id' => $course->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_course()
    {
        // Act
        $response = $this->postJson('/v1/courses', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'description',
                'instructor',
                'duration_hours',
                'status'
            ]);
    }

    /** @test */
    public function it_can_filter_courses_by_status()
    {
        // Arrange
        Course::factory()->create(['status' => 'published']);
        Course::factory()->create(['status' => 'draft']);

        // Act
        $response = $this->getJson('/v1/courses?status=published');

        // Assert
        $response->assertStatus(200);
        $courses = $response->json('data');
        
        foreach ($courses as $course) {
            $this->assertEquals('published', $course['status']);
        }
    }

    /** @test */
    public function it_can_search_courses_by_title()
    {
        // Arrange
        Course::factory()->create(['title' => 'Laravel Fundamentals']);
        Course::factory()->create(['title' => 'Vue.js Basics']);

        // Act
        $response = $this->getJson('/v1/courses?search=Laravel');

        // Assert
        $response->assertStatus(200);
        $courses = $response->json('data');
        
        $this->assertCount(1, $courses);
        $this->assertStringContainsString('Laravel', $courses[0]['title']);
    }
}
