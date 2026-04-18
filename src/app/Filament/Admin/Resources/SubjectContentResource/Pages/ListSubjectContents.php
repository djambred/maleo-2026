<?php

namespace App\Filament\Admin\Resources\SubjectContentResource\Pages;

use App\Filament\Admin\Resources\SubjectContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjectContents extends ListRecords
{
    protected static string $resource = SubjectContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
