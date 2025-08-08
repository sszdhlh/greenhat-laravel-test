<?php

namespace Modules\Course\Filament\Resources\CourseCategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Course\Filament\Resources\CourseCategoryResource;

class CreateCourseCategory extends CreateRecord
{
    protected static string $resource = CourseCategoryResource::class;
}
