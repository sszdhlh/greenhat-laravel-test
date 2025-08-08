<?php

namespace Modules\Course\Filament\Resources\CourseCategoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Modules\Course\Filament\Resources\CourseCategoryResource;

class ViewCourseCategory extends ViewRecord
{
    protected static string $resource = CourseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
