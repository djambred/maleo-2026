<?php

namespace App\Filament\Admin\Resources\ScoreResource\Pages;

use App\Filament\Admin\Resources\ScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScore extends EditRecord
{
    protected static string $resource = ScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
