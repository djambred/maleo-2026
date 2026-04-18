<?php

namespace App\Filament\Connect\Widgets;

use App\Models\Task;
use App\Models\ClassroomStudent;
use App\Models\TaskSubmission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ChildrenTasks extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = "Children's Upcoming Tasks";

    public function table(Table $table): Table
    {
        $guardian = auth()->user()->guardian;
        $classroomIds = collect();

        if ($guardian) {
            $studentIds = $guardian->students()->pluck('students.id');
            $classroomIds = ClassroomStudent::whereIn('student_id', $studentIds)->pluck('classroom_id');
        }

        $query = Task::query()
            ->where('is_published', true)
            ->whereIn('classroom_id', $classroomIds)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->orderBy('due_date');

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
