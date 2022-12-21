<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use NumberFormatter;

class BookingOverviewWidget extends BaseWidget
{
    public ?Booking $record = null;

    protected int|string|array $columnSpan = 5;

    protected function getCards(): array
    {
        if (! isset($this->record)) {
            return [];
        }

        $fmt = numfmt_create('de_DE', NumberFormatter::CURRENCY);

        return [
            Card::make(__('Apartment'), $this->record->apartment->__toString()),
            Card::make(__('Check-in').' - '.__('Check-out'),
                sprintf('%s - %s', $this->record->begins_at->format('d.m.y'), $this->record->ends_at->format('d.m.y'))
            ),
            Card::make(__('Days'), $this->record->days_count),
            Card::make(__('Price per night'), numfmt_format_currency($fmt, $this->record->price_per_day, 'EUR')),
            Card::make(__('Total'), numfmt_format_currency($fmt, $this->record->price_total, 'EUR')),
            Card::make(__('Paid at'), $this->record->is_paid ? $this->record->paid_at->format('d.m.Y H:i:s') : __('Not paid')),
        ];
    }
}
