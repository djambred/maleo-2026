<?php

namespace App\Filament\Connect\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.connect.widgets.welcome-widget';
}
