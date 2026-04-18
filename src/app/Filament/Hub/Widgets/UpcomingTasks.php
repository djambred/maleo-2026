<?php

namespace App\Filament\Hub\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingTasks extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Upcoming Deadlines';

    public function table(Table $table): Table
    {
        $user = auth()->user();

        $query = Task::query()
            ->where('is_published', true)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->orderBy('due_date');

        if ($user->hasRole('teacher') && $user->teacher) {
            $query->where('teacher_id', $user->teacher->id);
        }

        if ($user->hasRole('student') && $user->student) {
            $classroomIds = $user->student->classroomStudents()->pluck('classroom_id');
            $query->whereIn('classroom_id', $classroomIds);
        }

        return $table
            ->query($query->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->limit(30),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'assignment' => 'info',
                        'quiz' => 'warning',
                        'exam' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject'),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime('d M, H:i')
                    ->label('Due'),
            ])
            ->paginated(false);
    }
}
