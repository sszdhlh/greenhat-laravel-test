<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Base\Traits\CamelCasing;
use Modules\User\Models\User;

class Employee extends Model
{
    use CamelCasing, HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'role',
        'highest_qualification',
        'desired_salary',
        'note',
        'confirmation_email_sent_at'
    ];

    protected $casts = [
        'confirmation_email_sent_at' => 'datetime',
        'desired_salary' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function authenticationLogs(): MorphMany
    {
        return $this->user->authentications();
    }

    public static function getRoleOptions(): array
    {
        return [
            'Family Support Leader' => 'Family Support Leader',
        ];
    }

    public function username(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->user->name,
        );
    }

    protected static function newFactory()
    {
        return \Modules\Employee\Database\Factories\EmployeeFactory::new();
    }
}
