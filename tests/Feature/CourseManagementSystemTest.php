<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class CourseManagementSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Enable Course module for testing
        $this->artisan('module:enable Course');
    }

    /** @test */
    public function complete_course_management_workflow()
    {
        // Step 1: Create a course category
        $categoryData = [
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Courses related to web development',
            'is_active' => true
        ];

        $categoryResponse = $this->postJson('/v1/course-categories', $categoryData);
        $categoryResponse->assertStatus(201);
        $categoryId = $categoryResponse->json('data.id');

        // Step 2: Create a course with the category
        $courseData = [
            'title' => 'Laravel for Beginners',
            'description' => 'Learn Laravel framework from scratch',
            'instructor' => 'Jane Smith',
            'duration_hours' => 40,
            'max_participants' => 25,
            'status' => 'published',
            'price' => 299.99,
            'category_ids' => [$categoryId]
        ];

        $courseResponse = $this->postJson('/v1/courses', $courseData);
        $courseResponse->assertStatus(201);
        $courseId = $courseResponse->json('data.id');

        // Step 3: Verify the course is listed with proper relationships
        $listResponse = $this->getJson('/v1/courses');
        $listResponse->assertStatus(200)
            ->assertJsonPath('data.0.categories_count', 1);

        // Step 4: Verify the category shows course count
        $categoryListResponse = $this->getJson('/v1/course-categories');
        $categoryListResponse->assertStatus(200)
            ->assertJsonPath('data.0.courses_count', 1);

        // Step 5: Update the course
        $updateData = ['title' => 'Advanced Laravel Techniques'];
        $updateResponse = $this->putJson("/v1/courses/{$courseId}", $updateData);
        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.title', 'Advanced Laravel Techniques');

        // Step 6: Verify the update
        $showResponse = $this->getJson("/v1/courses/{$courseId}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.title', 'Advanced Laravel Techniques');
    }

    /** @test */
    public function course_filtering_and_search_functionality()
    {
        // Create test data
        $category = CourseCategory::factory()->create();
        
        Course::factory()->create([
            'title' => 'Laravel Fundamentals',
            'status' => 'published',
            'instructor' => 'John Doe'
        ]);
        
        Course::factory()->create([
            'title' => 'Vue.js Basics',
            'status' => 'draft',
            'instructor' => 'Jane Smith'
        ]);

        // Test status filtering
        $publishedResponse = $this->getJson('/v1/courses?status=published');
        $publishedResponse->assertStatus(200);
        $publishedCourses = $publishedResponse->json('data');
        $this->assertCount(1, $publishedCourses);
        $this->assertEquals('published', $publishedCourses[0]['status']);

        // Test search functionality
        $searchResponse = $this->getJson('/v1/courses?search=Laravel');
        $searchResponse->assertStatus(200);
        $searchResults = $searchResponse->json('data');
        $this->assertCount(1, $searchResults);
        $this->assertStringContainsString('Laravel', $searchResults[0]['title']);

        // Test instructor search
        $instructorResponse = $this->getJson('/v1/courses?search=John');
        $instructorResponse->assertStatus(200);
        $instructorResults = $instructorResponse->json('data');
        $this->assertCount(1, $instructorResults);
        $this->assertStringContainsString('John', $instructorResults[0]['instructor']);
    }

    /** @test */
    public function course_category_management()
    {
        // Test creating active and inactive categories
        $activeCategory = CourseCategory::factory()->active()->create();
        $inactiveCategory = CourseCategory::factory()->inactive()->create();

        // Test filtering active categories
        $activeResponse = $this->getJson('/v1/course-categories?active=1');
        $activeResponse->assertStatus(200);
        $activeCategories = $activeResponse->json('data');
        
        foreach ($activeCategories as $category) {
            $this->assertTrue($category['is_active']);
        }

        // Test slug uniqueness validation
        $duplicateResponse = $this->postJson('/v1/course-categories', [
            'name' => 'Test Category',
            'slug' => $activeCategory->slug,
            'description' => 'Test description'
        ]);
        
        $duplicateResponse->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }
}
