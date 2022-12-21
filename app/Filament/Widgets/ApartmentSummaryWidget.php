<?php

namespace App\Filament\Widgets;

use App\Models\Apartment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Str;
use NumberFormatter;

class ApartmentSummaryWidget extends BaseWidget
{
    public ?Apartment $record;

    protected function getCards(): array
    {
        if (! isset($this->record)) {
            return [];
        }
        $fmt = numfmt_create('de_DE', NumberFormatter::CURRENCY);

        $bookingsLastLastMonth = $this->record->bookings()->where('begins_at', 'like', now()->subMonths(2)->format('Y-m-%'))->get();
        $bookingsLastMonth = $this->record->bookings()->where('begins_at', 'like', now()->subMonth()->format('Y-m-%'))->get();
        $bookingsCurrentMonth = $this->record->bookings()->where('begins_at', 'like', now()->format('Y-m-%'))->get();

        $bookingDaysLastLastMonth = $bookingsLastLastMonth->sum('days_count');
        $bookingDaysLastMonth = $bookingsLastMonth->sum('days_count');
        $bookingDaysCurrentMonth = $bookingsCurrentMonth->sum('days_count');

        $daysDiffLastMonth = $bookingDaysLastMonth - $bookingDaysLastLastMonth;
        $daysDiffCurrentMonth = $bookingDaysCurrentMonth - $bookingDaysLastMonth;

        $bookingSumLastLastMonth = $bookingsLastLastMonth->sum('price_total');
        $bookingSumLastMonth = $bookingsLastMonth->sum('price_total');
        $bookingSumCurrentMonth = $bookingsCurrentMonth->sum('price_total');

        $incomeDiffLastMonth = $bookingSumLastMonth - $bookingSumLastLastMonth;
        $incomeDiffCurrentMonth = $bookingSumCurrentMonth - $bookingSumLastMonth;

        return [
            Card::make(__('Apartment'), sprintf('%s, %s', $this->record->__toString(), $this->record->zip)),
            Card::make(__('Status'), Str::ucfirst(__($this->record->status)))
                ->chartColor('success')
                ->description(__('Since').': '.$this->record->updated_at->format('d.m.Y')),
            Card::make(__('Booked days last month'), $bookingDaysLastMonth)
                ->description(($daysDiffLastMonth > 0 ? __('Increase') : __('Decrease')).' '.$daysDiffLastMonth)
                ->descriptionColor($daysDiffLastMonth > 0 ? 'success' : 'danger')
                ->descriptionIcon($daysDiffLastMonth > 0 ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down'),
            Card::make(__('Booked days current month'), $bookingDaysCurrentMonth)
                ->description(($daysDiffCurrentMonth > 0 ? __('Increase') : __('Decrease')).' '.$daysDiffCurrentMonth)
                ->descriptionColor($daysDiffCurrentMonth > 0 ? 'success' : 'danger')
                ->descriptionIcon($daysDiffCurrentMonth > 0 ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down'),
            Card::make(__('Income last month'), (numfmt_format_currency($fmt, $bookingSumLastMonth, 'EUR')))
                ->description(($incomeDiffLastMonth > 0 ? __('Increase') : __('Decrease')).' '.(numfmt_format_currency($fmt, $incomeDiffLastMonth, 'EUR')))
                ->descriptionColor($incomeDiffLastMonth > 0 ? 'success' : 'danger')
                ->descriptionIcon($incomeDiffLastMonth > 0 ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down'),
            Card::make(__('Income current month'), numfmt_format_currency($fmt, $bookingSumCurrentMonth, 'EUR'))
                ->description(($incomeDiffCurrentMonth > 0 ? __('Increase') : __('Decrease')).' '.(numfmt_format_currency($fmt, $incomeDiffCurrentMonth, 'EUR')))
                ->descriptionColor($incomeDiffCurrentMonth > 0 ? 'success' : 'danger')
                ->descriptionIcon($incomeDiffCurrentMonth > 0 ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down'),
            Card::make(__('Average nightly fee'), numfmt_format_currency($fmt, $this->record->bookings->avg('price_per_day'), 'EUR')),
            Card::make(__('Total income'), numfmt_format_currency($fmt, $this->record->bookings->sum('price_total'), 'EUR'))
                ->description(__('First booking').': '.$this->record->bookings->sortBy('begins_at')->first()->begins_at->format('d.m.Y')),
        ];
    }
}
