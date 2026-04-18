<?php

namespace App\Filament\Connect\Resources\AttendanceResource\Pages;

use App\Filament\Connect\Resources\AttendanceResource;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;
}
