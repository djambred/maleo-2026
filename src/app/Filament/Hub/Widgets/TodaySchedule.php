<?php

namespace App\Filament\Hub\Widgets;

use App\Models\Schedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodaySchedule extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = "Today's Schedule";

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $today = strtolower(now()->format('l'));

        $query = Schedule::query()->where('day', $today);

        if ($user->hasRole('teacher') && $user->teacher) {
            $query->where('teacher_id', $user->teacher->id);
        }

        if ($user->hasRole('student') && $user->student) {
            $classroomIds = $user->student->classroomStudents()->pluck('classroom_id');
            $query->whereIn('classroom_id', $classroomIds);
        }

        return $table
            ->query($query->orderBy('start_time'))
            ->columns([
                Tables\Columns\TextColumn::make('start_time')
                    ->time('H:i')
                    ->label('Time'),
                Tables\Columns\TextColumn::make('end_time')
                    ->time('H:i')
                    ->label('End'),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject'),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label('Class'),
                Tables\Columns\TextColumn::make('room')
                    ->label('Room'),
            ])
            ->paginated(false);
    }
}
