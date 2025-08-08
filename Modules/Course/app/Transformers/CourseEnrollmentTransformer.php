<?php

namespace Modules\Course\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Employee\Models\Employee;

class CourseEnrollmentTransformer extends TransformerAbstract
{
    public function transform(Employee $employee): array
    {
        return [
            'id' => $employee->id,
            'name' => $employee->name ?? '',
            'email' => $employee->email ?? '',
            'status' => $employee->pivot->status ?? 'enrolled',
            'enrolled_at' => $employee->pivot->enrolled_at ? $employee->pivot->enrolled_at->toAtomString() : null,
            'completed_at' => $employee->pivot->completed_at ? $employee->pivot->completed_at->toAtomString() : null,
            'created_at' => $employee->pivot->created_at ? $employee->pivot->created_at->toAtomString() : null,
            'updated_at' => $employee->pivot->updated_at ? $employee->pivot->updated_at->toAtomString() : null,
        ];
    }
}
