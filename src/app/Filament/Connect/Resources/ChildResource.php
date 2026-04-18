<?php

namespace App\Filament\Connect\Resources;

use App\Filament\Connect\Resources\ChildResource\Pages;
use App\Models\Student;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChildResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'My Children';

    protected static ?string $navigationLabel = 'Children';

    protected static ?string $modelLabel = 'Child';

    protected static ?string $pluralModelLabel = 'Children';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS'),
                Tables\Columns\TextColumn::make('classroomStudents.classroom.name')
                    ->label('Classroom')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'graduated' => 'info',
                        'transferred' => 'warning',
                    }),
            ])
            ->filters([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChildren::route('/'),
        ];
    }
}
