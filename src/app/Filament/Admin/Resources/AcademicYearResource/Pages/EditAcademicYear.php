<?php

namespace App\Filament\Admin\Resources\AcademicYearResource\Pages;

use App\Filament\Admin\Resources\AcademicYearResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicYear extends EditRecord
{
    protected static string $resource = AcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
