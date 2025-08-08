<?php

namespace Modules\Course\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Course\Models\CourseCategory;

class CourseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technical',
                'description' => 'Technical skills and programming courses',
                'slug' => 'technical',
            ],
            [
                'name' => 'Leadership',
                'description' => 'Leadership and management development courses',
                'slug' => 'leadership',
            ],
            [
                'name' => 'Communication',
                'description' => 'Communication and interpersonal skills courses',
                'slug' => 'communication',
            ],
            [
                'name' => 'Professional Development',
                'description' => 'General professional development and career growth courses',
                'slug' => 'professional-development',
            ],
            [
                'name' => 'Compliance',
                'description' => 'Regulatory compliance and legal requirement courses',
                'slug' => 'compliance',
            ],
        ];

        foreach ($categories as $category) {
            CourseCategory::create($category);
        }
    }
}
