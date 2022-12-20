<?php

namespace App\Filament;

use Filament\Facades\Filament;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-template';

    protected static ?int $navigationSort = -2;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament::pages.dashboard';

    protected static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? __('filament::pages/dashboard.title');
    }

    protected function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    protected function getColumns(): int|array
    {
        return 2;
    }

    protected function getTitle(): string
    {
        return static::$title ?? __('filament::pages/dashboard.title');
    }
}
