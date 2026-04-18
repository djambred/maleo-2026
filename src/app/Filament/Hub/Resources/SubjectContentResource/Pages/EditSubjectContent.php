<?php

namespace App\Filament\Hub\Resources\SubjectContentResource\Pages;

use App\Filament\Hub\Resources\SubjectContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubjectContent extends EditRecord
{
    protected static string $resource = SubjectContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
