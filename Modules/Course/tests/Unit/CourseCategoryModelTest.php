<?php

namespace Modules\Course\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class CourseCategoryModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_many_courses()
    {
        // Arrange
        $category = CourseCategory::factory()->create();
        $courses = Course::factory()->count(3)->create();

        // Act
        $category->courses()->attach($courses->pluck('id'));

        // Assert
        $this->assertCount(3, $category->courses);
        $this->assertInstanceOf(Course::class, $category->courses->first());
    }

    /** @test */
    public function it_automatically_generates_slug_from_name()
    {
        // Arrange & Act
        $category = CourseCategory::factory()->create([
            'name' => 'Web Development Basics'
        ]);

        // Assert
        $this->assertEquals('web-development-basics', $category->slug);
    }

    /** @test */
    public function it_can_scope_active_categories()
    {
        // Arrange
        CourseCategory::factory()->create(['is_active' => true]);
        CourseCategory::factory()->create(['is_active' => false]);

        // Act
        $activeCategories = CourseCategory::active()->get();

        // Assert
        $this->assertCount(1, $activeCategories);
        $this->assertTrue($activeCategories->first()->is_active);
    }

    /** @test */
    public function it_casts_is_active_to_boolean()
    {
        // Arrange & Act
        $category = CourseCategory::factory()->create(['is_active' => '1']);

        // Assert
        $this->assertIsBool($category->is_active);
        $this->assertTrue($category->is_active);
    }
}
