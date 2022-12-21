<?php

namespace App\Filament\Widgets;

use App\Invoices\BookingSingleInvoice;
use App\Models\Booking;
use Closure;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UnpaidBookings extends BaseWidget
{
    protected int|string|array $columnSpan = 12;

    protected function getTableQuery(): Builder
    {
        return Booking::query()->whereNull('paid_at')
            ->orderBy('ends_at')
            ->orderBy('begins_at');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('apartment.name')
                ->label(__('Apartment'))
                ->sortable(),
            Tables\Columns\TextColumn::make('begins_at')
                ->label('Check-In')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('ends_at')
                ->label('Check-Out')
                ->date(),
            Tables\Columns\TextColumn::make('days_count')
                ->label('Nächte'),
            Tables\Columns\TextColumn::make('price_per_day')
                ->label('Preis pro Tag')
                ->money('EUR', true),
            Tables\Columns\TextColumn::make('price_total')
                ->label('Gesamt')
                ->money('EUR', true),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('download')
                ->url(fn (Booking $record): string => route('get.bookings.single.invoice', $record))
                ->icon('heroicon-s-download')
                ->color('secondary')
                ->openUrlInNewTab(),
            Action::make('paid')
                ->label(__('paid'))
                ->color('success')
                ->icon('heroicon-o-currency-euro')
                ->action(function (Booking $record) {
                    return $record->update(['paid_at' => now()]);
                }),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\DeleteBulkAction::make('delete'),
            BulkAction::make('download-bulk')
                ->label('Download all')
                ->icon('heroicon-s-download')
                ->color('secondary')
                ->action(function (Collection $records) {
                    $archive = new \ZipArchive;
                    $filename = 'mieten_export_'.now()->format('Y-m-d').'.zip';
                    $archive->open($filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                    foreach ($records as $record) {
                        $invoice = (new BookingSingleInvoice)($record)->render()->save();
                        $name = $invoice->filename;
                        $full_file = \Storage::path($name);
                        $archive->addFile($full_file, $name);
                    }
                    $archive->close();

                    return response()->download($filename);
                })
                ->requiresConfirmation(false)
                ->deselectRecordsAfterCompletion(),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (Booking $record): string => route('filament.resources.bookings.view', ['record' => $record]);
    }
}
