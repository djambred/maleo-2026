<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\Classroom;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', Student::count())
                ->description('Active: ' . Student::where('status', 'active')->count())
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Total Teachers', Teacher::count())
                ->description('Active: ' . Teacher::where('status', 'active')->count())
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([3, 5, 2, 7, 4, 6, 3]),

            Stat::make('Total Guardians', Guardian::count())
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make('Classrooms', Classroom::count())
                ->description('Avg capacity: ' . round(Classroom::avg('capacity') ?? 0))
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary'),
        ];
    }
}
