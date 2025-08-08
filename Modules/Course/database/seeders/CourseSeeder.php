<?php

namespace Modules\Course\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Laravel Advanced Techniques',
                'description' => 'Deep dive into advanced Laravel features including queues, events, and package development.',
                'instructor' => 'John Smith',
                'duration_hours' => 40,
                'max_participants' => 20,
                'status' => 'published',
                'start_date' => '2025-09-01',
                'end_date' => '2025-09-30',
                'price' => 299.99,
                'categories' => ['technical'],
            ],
            [
                'title' => 'Leadership Fundamentals',
                'description' => 'Essential leadership skills for new managers and team leads.',
                'instructor' => 'Sarah Johnson',
                'duration_hours' => 24,
                'max_participants' => 15,
                'status' => 'published',
                'start_date' => '2025-08-15',
                'end_date' => '2025-08-30',
                'price' => 199.99,
                'categories' => ['leadership'],
            ],
            [
                'title' => 'Effective Communication Skills',
                'description' => 'Improve your written and verbal communication skills in professional settings.',
                'instructor' => 'Michael Brown',
                'duration_hours' => 16,
                'max_participants' => 25,
                'status' => 'published',
                'start_date' => '2025-08-20',
                'end_date' => '2025-08-25',
                'price' => 149.99,
                'categories' => ['communication'],
            ],
            [
                'title' => 'Project Management Essentials',
                'description' => 'Learn the fundamentals of project management including planning, execution, and monitoring.',
                'instructor' => 'Emily Davis',
                'duration_hours' => 32,
                'max_participants' => 18,
                'status' => 'published',
                'start_date' => '2025-09-10',
                'end_date' => '2025-10-10',
                'price' => 249.99,
                'categories' => ['professional-development', 'leadership'],
            ],
            [
                'title' => 'Data Privacy and GDPR Compliance',
                'description' => 'Understanding data protection regulations and implementing compliance measures.',
                'instructor' => 'Robert Wilson',
                'duration_hours' => 12,
                'max_participants' => 30,
                'status' => 'published',
                'start_date' => '2025-08-25',
                'end_date' => '2025-08-27',
                'price' => 99.99,
                'categories' => ['compliance'],
            ],
        ];

        foreach ($courses as $courseData) {
            $categories = $courseData['categories'];
            unset($courseData['categories']);

            $course = Course::create($courseData);

            // Attach categories
            foreach ($categories as $categorySlug) {
                $category = CourseCategory::where('slug', $categorySlug)->first();
                if ($category) {
                    $course->categories()->attach($category->id);
                }
            }
        }
    }
}
