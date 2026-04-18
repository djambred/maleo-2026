<?php

namespace App\Filament\Connect\Resources;

use App\Filament\Connect\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Communication';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('audience')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'all' => 'primary',
                        'teachers' => 'info',
                        'students' => 'success',
                        'parents' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('By'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->defaultSort('published_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
        ];
    }
}
