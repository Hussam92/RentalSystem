<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Widgets\BookingOverviewWidget;
use App\Models\Booking;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Form;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property Booking $record
 */
class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('paid')
                ->label(__('paid'))
                ->color('success')
                ->icon('heroicon-o-currency-euro')
                ->visible(fn (): bool => ! $this->record->is_paid)
                ->action(function () {
                    $success = $this->record->update(['paid_at' => now()]);

                    Notification::make()
                        ->success()
                        ->title(__('received payment'))
                        ->body(__('The booking was marked as paid'))
                        ->send();

                    return $success;
                }),
            EditAction::make(),
            Action::make('download')
                ->url(fn (): string => route('get.bookings.single.download', $this->record))
                ->icon('heroicon-s-download')
                ->color('secondary')
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BookingOverviewWidget::class,
        ];
    }

    protected function getVisibleHeaderWidgets(): array
    {
        return $this->getHeaderWidgets();
    }

    protected function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }

    protected function form(Form $form): Form
    {
        return $form->schema([

        ]);
    }

    protected function getTitle(): string
    {
        return __('View booking');
    }

    protected function getHeading(): string|Htmlable
    {
        return sprintf('%s %s, %s', __('Apartment'), $this->record->apartment->__toString(), $this->record->apartment->zip);
    }

    protected function getSubheading(): string|Htmlable|null
    {
        return __('From - To: ').$this->record->begins_at->format('d.m.Y').' - '.$this->record->ends_at->format('d.m.Y');
    }
}
