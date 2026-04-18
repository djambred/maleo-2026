<?php

namespace App\Filament\Hub\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.hub.widgets.welcome-widget';
}
