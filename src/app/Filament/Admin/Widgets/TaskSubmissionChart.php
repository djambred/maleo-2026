<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Task;
use App\Models\TaskSubmission;
use Filament\Widgets\ChartWidget;

class TaskSubmissionChart extends ChartWidget
{
    protected static ?string $heading = 'Task Submissions Overview';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $submitted = TaskSubmission::where('status', 'submitted')->count();
        $graded = TaskSubmission::where('status', 'graded')->count();
        $returned = TaskSubmission::where('status', 'returned')->count();
        $late = TaskSubmission::where('status', 'late')->count();

        return [
            'datasets' => [
                [
                    'data' => [$submitted, $graded, $returned, $late],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                    ],
                ],
            ],
            'labels' => ['Submitted', 'Graded', 'Returned', 'Late'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
