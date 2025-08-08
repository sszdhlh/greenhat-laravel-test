<?php

namespace Modules\Course\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class CourseCategoryApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Enable Course module for testing
        $this->artisan('module:enable Course');
    }

    /** @test */
    public function it_can_list_course_categories()
    {
        // Arrange
        CourseCategory::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/v1/course-categories');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'is_active',
                        'courses_count',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_create_a_course_category()
    {
        // Arrange
        $categoryData = [
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Courses related to web development technologies',
            'is_active' => true
        ];

        // Act
        $response = $this->postJson('/v1/course-categories', $categoryData);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Course category created successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'is_active'
                ]
            ]);

        $this->assertDatabaseHas('course_categories', [
            'name' => 'Web Development',
            'slug' => 'web-development'
        ]);
    }

    /** @test */
    public function it_can_show_a_single_course_category()
    {
        // Arrange
        $category = CourseCategory::factory()->create();

        // Act
        $response = $this->getJson("/v1/course-categories/{$category->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name
                ]
            ]);
    }

    /** @test */
    public function it_can_update_a_course_category()
    {
        // Arrange
        $category = CourseCategory::factory()->create();
        $updateData = [
            'name' => 'Updated Category Name',
            'description' => 'Updated description'
        ];

        // Act
        $response = $this->putJson("/v1/course-categories/{$category->id}", $updateData);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Course category updated successfully'
            ]);

        $this->assertDatabaseHas('course_categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name'
        ]);
    }

    /** @test */
    public function it_can_delete_a_course_category()
    {
        // Arrange
        $category = CourseCategory::factory()->create();

        // Act
        $response = $this->deleteJson("/v1/course-categories/{$category->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Course category deleted successfully'
            ]);

        $this->assertSoftDeleted('course_categories', [
            'id' => $category->id
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_category()
    {
        // Act
        $response = $this->postJson('/v1/course-categories', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'slug'
            ]);
    }

    /** @test */
    public function it_ensures_unique_slug_when_creating_category()
    {
        // Arrange
        CourseCategory::factory()->create(['slug' => 'existing-slug']);

        // Act
        $response = $this->postJson('/v1/course-categories', [
            'name' => 'Test Category',
            'slug' => 'existing-slug',
            'description' => 'Test description'
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    /** @test */
    public function it_can_filter_active_categories()
    {
        // Arrange
        CourseCategory::factory()->create(['is_active' => true]);
        CourseCategory::factory()->create(['is_active' => false]);

        // Act
        $response = $this->getJson('/v1/course-categories?active=1');

        // Assert
        $response->assertStatus(200);
        $categories = $response->json('data');
        
        foreach ($categories as $category) {
            $this->assertTrue($category['is_active']);
        }
    }
}
