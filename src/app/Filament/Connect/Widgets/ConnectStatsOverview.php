<?php

namespace App\Filament\Connect\Widgets;

use App\Models\Attendance;
use App\Models\Task;
use App\Models\TaskSubmission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConnectStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $guardian = auth()->user()->guardian;
        if (!$guardian) {
            return [];
        }

        $studentIds = $guardian->students()->pluck('students.id');
        $childCount = $studentIds->count();

        $totalSubmissions = TaskSubmission::whereIn('student_id', $studentIds)->count();
        $gradedSubmissions = TaskSubmission::whereIn('student_id', $studentIds)
            ->where('status', 'graded')
            ->count();

        $avgScore = TaskSubmission::whereIn('student_id', $studentIds)
            ->whereNotNull('score')
            ->avg('score');

        $todayAttendance = Attendance::whereIn('student_id', $studentIds)
            ->where('date', now()->toDateString())
            ->first();

        return [
            Stat::make('My Children', $childCount)
                ->description('Registered students')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Task Submissions', $totalSubmissions)
                ->description('Graded: ' . $gradedSubmissions)
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('Average Score', $avgScore ? number_format($avgScore, 1) : '-')
                ->description('From graded tasks')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($avgScore && $avgScore >= 75 ? 'success' : 'warning'),

            Stat::make("Today's Attendance", $todayAttendance?->status ?? 'No data')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color(match ($todayAttendance?->status) {
                    'present' => 'success',
                    'absent' => 'danger',
                    'late' => 'warning',
                    'excused' => 'info',
                    default => 'gray',
                }),
        ];
    }
}
