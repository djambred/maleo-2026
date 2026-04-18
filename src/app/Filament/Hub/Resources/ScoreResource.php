<?php

namespace App\Filament\Hub\Resources;

use App\Filament\Hub\Resources\ScoreResource\Pages;
use App\Models\Score;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScoreResource extends Resource
{
    protected static ?string $model = Score::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Academic';

    protected static ?string $navigationLabel = 'My Scores';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('semester.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('score_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'daily' => 'gray',
                        'assignment' => 'info',
                        'midterm' => 'warning',
                        'final' => 'success',
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('score_type')
                    ->options([
                        'daily' => 'Daily',
                        'assignment' => 'Assignment',
                        'midterm' => 'Midterm',
                        'final' => 'Final',
                    ]),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScores::route('/'),
        ];
    }
}
