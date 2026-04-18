<?php

namespace App\Filament\Connect\Resources\ChildResource\Pages;

use App\Filament\Connect\Resources\ChildResource;
use Filament\Resources\Pages\ListRecords;

class ListChildren extends ListRecords
{
    protected static string $resource = ChildResource::class;
}
