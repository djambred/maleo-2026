<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Schedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AcademicStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $activeAy = AcademicYear::where('is_active', true)->first();
        $activeSemester = Semester::where('is_active', true)->first();

        return [
            Stat::make('Academic Year', $activeAy?->name ?? '-')
                ->description($activeAy ? ($activeAy->is_active ? 'Active' : 'Inactive') : 'No active year')
                ->descriptionIcon('heroicon-m-calendar')
                ->color($activeAy?->is_active ? 'success' : 'gray'),

            Stat::make('Current Semester', $activeSemester?->name ?? '-')
                ->description($activeSemester ? $activeSemester->start_date->format('d M') . ' - ' . $activeSemester->end_date->format('d M Y') : '')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('Subjects', Subject::count())
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning'),

            Stat::make('Schedule Entries', Schedule::count())
                ->description($activeSemester ? 'Semester: ' . $activeSemester->name : '')
                ->descriptionIcon('heroicon-m-table-cells')
                ->color('primary'),
        ];
    }
}
