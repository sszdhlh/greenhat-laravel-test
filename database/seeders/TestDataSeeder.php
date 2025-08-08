<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create course categories
        $categories = [
            [
                'name' => 'Technical Skills', 
                'description' => 'Programming and technical courses',
                'slug' => 'technical-skills',
                'is_active' => true
            ],
            [
                'name' => 'Leadership', 
                'description' => 'Management and leadership development',
                'slug' => 'leadership',
                'is_active' => true
            ],
            [
                'name' => 'Communication', 
                'description' => 'Communication and soft skills',
                'slug' => 'communication',
                'is_active' => true
            ],
        ];

        foreach ($categories as $categoryData) {
            CourseCategory::create($categoryData);
        }

        // Create some test users and employees
        $users = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            Employee::create([
                'user_id' => $user->id,
                'role' => 'Developer',
                'highest_qualification' => 'Bachelor\'s Degree',
                'desired_salary' => 50000,
                'note' => 'Test employee',
            ]);
        }

        // Create courses
        $courses = [
            [
                'title' => 'Laravel Development Basics',
                'description' => 'Learn the fundamentals of Laravel framework',
                'instructor' => 'John Instructor',
                'duration_hours' => 40,
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(37),
                'max_participants' => 20,
                'status' => 'published',
                'price' => 299.99,
                'is_active' => true,
            ],
            [
                'title' => 'Leadership Skills',
                'description' => 'Develop essential leadership and management skills',
                'instructor' => 'Sarah Manager',
                'duration_hours' => 24,
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(38),
                'max_participants' => 15,
                'status' => 'published',
                'price' => 199.99,
                'is_active' => true,
            ],
            [
                'title' => 'Effective Communication',
                'description' => 'Improve your communication skills in the workplace',
                'instructor' => 'Mike Speaker',
                'duration_hours' => 16,
                'start_date' => now()->addDays(21),
                'end_date' => now()->addDays(37),
                'max_participants' => 25,
                'status' => 'draft',
                'price' => 149.99,
                'is_active' => true,
            ],
        ];

        $categories = CourseCategory::all();
        
        foreach ($courses as $courseData) {
            $course = Course::create($courseData);
            
            // Attach random categories
            $course->categories()->attach(
                $categories->random(rand(1, 2))->pluck('id')->toArray()
            );
        }

        $this->command->info('Test data created successfully!');
        $this->command->info('- 3 course categories');
        $this->command->info('- 2 test employees');
        $this->command->info('- 3 courses with categories');
    }
}
