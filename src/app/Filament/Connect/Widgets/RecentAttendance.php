<?php

namespace App\Filament\Connect\Widgets;

use App\Models\Attendance;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentAttendance extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Recent Attendance';

    public function table(Table $table): Table
    {
        $guardian = auth()->user()->guardian;
        $studentIds = $guardian ? $guardian->students()->pluck('students.id') : collect();

        $query = Attendance::query()
            ->whereIn('student_id', $studentIds)
            ->latest('date');

        return $table
            ->query($query->limit(7))
            ->columns([
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Child'),
                Tables\Columns\TextColumn::make('date')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info',
                    }),
            ])
            ->paginated(false);
    }
}
