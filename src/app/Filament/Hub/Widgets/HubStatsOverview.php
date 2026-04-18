<?php

namespace App\Filament\Hub\Widgets;

use App\Models\Schedule;
use App\Models\SubjectContent;
use App\Models\Task;
use App\Models\TaskSubmission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HubStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user->hasRole('teacher')) {
            return $this->getTeacherStats();
        }

        return $this->getStudentStats();
    }

    protected function getTeacherStats(): array
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return [];
        }

        $myContents = SubjectContent::where('teacher_id', $teacher->id)->count();
        $myTasks = Task::where('teacher_id', $teacher->id)->count();
        $pendingSubmissions = TaskSubmission::whereHas('task', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->where('status', 'submitted')
            ->count();
        $mySchedules = Schedule::where('teacher_id', $teacher->id)->count();

        return [
            Stat::make('My Content', $myContents)
                ->description('Published materials')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('My Tasks', $myTasks)
                ->description('Assignments & quizzes')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),

            Stat::make('Pending Grading', $pendingSubmissions)
                ->description('Submissions to review')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color($pendingSubmissions > 0 ? 'warning' : 'success'),

            Stat::make('Schedule Slots', $mySchedules)
                ->description('Weekly classes')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),
        ];
    }

    protected function getStudentStats(): array
    {
        $student = auth()->user()->student;
        if (!$student) {
            return [];
        }

        $classroomIds = $student->classroomStudents()->pluck('classroom_id');

        $availableTasks = Task::where('is_published', true)
            ->whereIn('classroom_id', $classroomIds)
            ->count();

        $submitted = TaskSubmission::where('student_id', $student->id)->count();

        $pendingTasks = Task::where('is_published', true)
            ->whereIn('classroom_id', $classroomIds)
            ->whereDoesntHave('submissions', fn ($q) => $q->where('student_id', $student->id))
            ->count();

        $avgScore = TaskSubmission::where('student_id', $student->id)
            ->whereNotNull('score')
            ->avg('score');

        return [
            Stat::make('Available Tasks', $availableTasks)
                ->description('From all subjects')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),

            Stat::make('Submitted', $submitted)
                ->description('Your submissions')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('success'),

            Stat::make('Pending', $pendingTasks)
                ->description('Not yet submitted')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($pendingTasks > 0 ? 'warning' : 'success'),

            Stat::make('Avg Score', $avgScore ? number_format($avgScore, 1) : '-')
                ->description('From graded tasks')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
        ];
    }
}
