<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Announcement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAnnouncements extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 2;

    protected static ?string $heading = 'Latest Announcements';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Announcement::query()
                    ->where('is_active', true)
                    ->latest('published_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
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
            ->paginated(false);
    }
}
