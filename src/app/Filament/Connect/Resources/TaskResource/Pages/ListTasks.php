<?php

namespace App\Filament\Connect\Resources\TaskResource\Pages;

use App\Filament\Connect\Resources\TaskResource;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;
}
