<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getColumns(): int | array
    {
        // Grid 12 kolom supaya bisa diatur proporsinya lebih fleksibel
        return 2;
    }
}
