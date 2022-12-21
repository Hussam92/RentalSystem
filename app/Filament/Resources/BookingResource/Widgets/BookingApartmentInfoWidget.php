<?php

namespace App\Filament\Resources\BookingResource\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget;

class BookingApartmentInfoWidget extends StatsOverviewWidget
{
    public ?Booking $record = null;

    protected function getCards(): array
    {
        return [
            StatsOverviewWidget\Card::make(__('Apartment'), $this->record->apartment->__toString()),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
