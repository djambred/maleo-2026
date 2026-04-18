<?php

namespace App\Filament\Hub\Widgets;

use App\Models\Announcement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class HubAnnouncements extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 2;

    protected static ?string $heading = 'Announcements';

    public function table(Table $table): Table
    {
        $user = auth()->user();

        $query = Announcement::query()
            ->where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('audience', 'all');
                if ($user->hasRole('teacher')) {
                    $q->orWhere('audience', 'teachers');
                }
                if ($user->hasRole('student')) {
                    $q->orWhere('audience', 'students');
                }
            })
            ->latest('published_at');

        return $table
            ->query($query->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->limit(60),
                Tables\Columns\TextColumn::make('audience')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'all' => 'primary',
                        'teachers' => 'info',
                        'students' => 'success',
                        'parents' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('d M Y')
                    ->label('Date'),
            ])
            ->paginated(false);
    }
}
