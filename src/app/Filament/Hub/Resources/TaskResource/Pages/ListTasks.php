<?php

namespace App\Filament\Hub\Resources\TaskResource\Pages;

use App\Filament\Hub\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()->hasRole('teacher')),
        ];
    }
}
