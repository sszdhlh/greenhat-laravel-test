<?php

namespace Modules\Course\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Course\Models\CourseCategory;

class CourseCategoryTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'courses',
    ];

    public function transform($data): array
    {
        $model = $data instanceof CourseCategory ? $data : new CourseCategory;

        return [
            'id' => $model->id ? (int) $model->id : null,
            'name' => $model->name ?? '',
            'description' => $model->description,
            'is_active' => (bool) ($model->is_active ?? true),
            'courses_count' => $model->courses_count ?? ($model->courses ? $model->courses->count() : 0),
            'created_at' => $model->created_at?->toISOString(),
            'updated_at' => $model->updated_at?->toISOString(),
        ];
    }

    public function includeCourses($category)
    {
        return $this->collection($category->courses, new CourseTransformer);
    }
}
