<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;

class AttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Attendance This Week';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $startOfWeek = now()->startOfWeek();

        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $present = [];
        $absent = [];

        for ($i = 0; $i < 6; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();
            $present[] = Attendance::where('date', $date)->where('status', 'present')->count();
            $absent[] = Attendance::where('date', $date)->whereIn('status', ['absent', 'late'])->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Present',
                    'data' => $present,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.6)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'Absent/Late',
                    'data' => $absent,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.6)',
                    'borderColor' => 'rgb(239, 68, 68)',
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
