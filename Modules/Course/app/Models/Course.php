<?php

namespace Modules\Course\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Base\Traits\CamelCasing;
use Modules\Course\Database\Factories\CourseFactory;
use Modules\Employee\Models\Employee;

class Course extends Model
{
    use CamelCasing, HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'content',
        'instructor',
        'duration_hours',
        'max_participants',
        'status',
        'start_date',
        'end_date',
        'price',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_hours' => 'integer',
        'max_participants' => 'integer',
        'price' => 'float',
        'is_active' => 'boolean',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(CourseCategory::class, 'course_category_pivot');
    }

    public function enrolledEmployees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'course_enrollments')
            ->withPivot(['enrolled_at', 'completed_at', 'status'])
            ->withTimestamps();
    }

    protected static function newFactory(): CourseFactory
    {
        return CourseFactory::new();
    }
}
