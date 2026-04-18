<?php

namespace App\Filament\Hub\Resources\SubjectContentResource\Pages;

use App\Filament\Hub\Resources\SubjectContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjectContents extends ListRecords
{
    protected static string $resource = SubjectContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()->hasRole('teacher')),
        ];
    }
}
