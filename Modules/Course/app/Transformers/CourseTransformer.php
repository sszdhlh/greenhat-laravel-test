<?php

namespace Modules\Course\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Course\Models\Course;
use Modules\Course\Transformers\CourseCategoryTransformer;
use Modules\Course\Transformers\CourseEnrollmentTransformer;

class CourseTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'categories',
        'enrollments',
    ];

    public function transform(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->title ?? '',
            'description' => $course->description ?? '',
            'content' => $course->content ?? '',
            'instructor' => $course->instructor ?? '',
            'duration_hours' => $course->duration_hours ?? 0,
            'max_participants' => $course->max_participants,
            'status' => $course->status ?? 'draft',
            'start_date' => $course->start_date?->toAtomString(),
            'end_date' => $course->end_date?->toAtomString(),
            'price' => $course->price ? (float) $course->price : null,
            'is_active' => (bool) ($course->is_active ?? true),
            'categories_count' => $course->categories_count ?? ($course->relationLoaded('categories') ? $course->categories->count() : 0),
            'enrollments_count' => $course->enrollments_count ?? ($course->relationLoaded('enrolledEmployees') ? $course->enrolledEmployees->count() : 0),
            'created_at' => $course->created_at?->toAtomString(),
            'updated_at' => $course->updated_at?->toAtomString(),
        ];
    }

    public function includeCategories(Course $course)
    {
        if ($course->relationLoaded('categories')) {
            return $this->collection($course->categories, new CourseCategoryTransformer());
        }
        return $this->null();
    }

    public function includeEnrollments(Course $course)
    {
        if ($course->relationLoaded('enrolledEmployees')) {
            return $this->collection($course->enrolledEmployees, new CourseEnrollmentTransformer());
        }
        return $this->null();
    }
}
