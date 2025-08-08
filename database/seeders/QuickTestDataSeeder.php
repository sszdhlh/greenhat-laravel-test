<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

class QuickTestDataSeeder extends Seeder
{
    public function run()
    {
        // Create test users and employees
        $userData = [
            ['first_name' => 'John', 'last_name' => 'Smith', 'email' => 'john.smith@company.com'],
            ['first_name' => 'Sarah', 'last_name' => 'Johnson', 'email' => 'sarah.johnson@company.com'],
            ['first_name' => 'Mike', 'last_name' => 'Wilson', 'email' => 'mike.wilson@company.com'],
        ];

        foreach ($userData as $data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt('password'),
            ]);

            Employee::create([
                'user_id' => $user->id,
                'role' => ['Developer', 'Manager', 'Designer'][array_rand(['Developer', 'Manager', 'Designer'])],
                'highest_qualification' => "Bachelor's Degree",
                'desired_salary' => rand(40000, 80000),
                'note' => 'Test employee created by seeder',
            ]);
        }

        // Create test courses
        $categories = CourseCategory::all();
        
        if ($categories->count() > 0) {
            $courses = [
                [
                    'title' => 'Laravel Development Fundamentals',
                    'description' => 'Learn the basics of Laravel framework development',
                    'content' => 'This course covers Laravel routing, controllers, models, and views.',
                    'instructor' => 'John Instructor',
                    'duration_hours' => 40,
                    'max_participants' => 25,
                    'status' => 'published',
                    'start_date' => now()->addDays(10),
                    'end_date' => now()->addDays(50),
                    'price' => 299.99,
                    'is_active' => true,
                ],
                [
                    'title' => 'Advanced PHP Programming',
                    'description' => 'Master advanced PHP concepts and best practices',
                    'content' => 'Covers OOP, design patterns, testing, and performance optimization.',
                    'instructor' => 'Sarah Expert',
                    'duration_hours' => 60,
                    'max_participants' => 20,
                    'status' => 'published',
                    'start_date' => now()->addDays(20),
                    'end_date' => now()->addDays(80),
                    'price' => 399.99,
                    'is_active' => true,
                ],
                [
                    'title' => 'Project Management Essentials',
                    'description' => 'Learn essential project management skills for tech teams',
                    'content' => 'Agile methodologies, team leadership, and project planning.',
                    'instructor' => 'Mike Manager',
                    'duration_hours' => 24,
                    'max_participants' => 15,
                    'status' => 'draft',
                    'start_date' => now()->addDays(30),
                    'end_date' => now()->addDays(60),
                    'price' => 199.99,
                    'is_active' => true,
                ],
            ];

            foreach ($courses as $courseData) {
                $course = Course::create($courseData);
                
                // Attach random categories to each course
                $randomCategories = $categories->random(rand(1, min(2, $categories->count())));
                $course->categories()->attach($randomCategories->pluck('id')->toArray());
            }
        }

        $this->command->info('Quick test data created successfully!');
        $this->command->info('- 3 employees with users');
        $this->command->info('- 3 courses with categories');
    }
}
