<?php

namespace App\Filament\Resources\ApartmentResource\RelationManagers;

use App\Invoices\BookingSingleInvoice;
use App\Models\Apartment;
use App\Models\Booking;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $recordTitleAttribute = 'apartment_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('begins_at')
                    ->label('Check-in')
                    ->required(),
                Forms\Components\DatePicker::make('ends_at')
                    ->label('Check-out')
                    ->after('begins_at')
                    ->required(),
                Forms\Components\TextInput::make('price_per_day')->mask(fn (Mask $mask) => $mask
                    ->patternBlocks([
                        'money' => fn (Mask $mask) => $mask
                            ->numeric()
                            ->thousandsSeparator('.')
                            ->decimalSeparator(',')
                            ->normalizeZeros(),
                    ])
                    ->pattern('€ money'),
                )->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
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
            ])
            ->filters([
                SelectFilter::make('apartment_id')
                    ->label('Wohnung')
                    ->options(Apartment::all()->mapWithKeys(fn (Apartment $apartment) => [$apartment->id => $apartment->__toString()])),
                Filter::make('check-in')
                    ->form([
                        Forms\Components\DatePicker::make('begins_at')->label('Check-in'),
                        Forms\Components\DatePicker::make('ends_at')->label('Check-out'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['begins_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('begins_at', '>=', $date),
                            )
                            ->when(
                                $data['ends_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('ends_at', '<=', $date),
                            );
                    }),
                Filter::make('month')
                    ->form([
                        Forms\Components\Select::make('begins_at')->label('Monat')->options(
                            Booking::get()->sortBy('begins_at')->pluck('begins_at')
                                ->mapWithKeys(function (Carbon $carbon) {
                                    $newCarbon = $carbon->clone()->startOfMonth();
                                    $filter = $newCarbon->format('Y-m');
                                    $text = $newCarbon->locale('de')->translatedFormat('F Y');

                                    return [$filter => $text];
                                })),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['begins_at'],
                                fn (Builder $query, $date): Builder => $query->where('begins_at', 'like', $date.'%'),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            ]);
    }
}
