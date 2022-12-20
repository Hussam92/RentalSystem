<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalResource\Pages;
use App\Models\Apartment;
use App\Models\Rental;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;

class RentalResource extends Resource
{
    protected static ?string $model = Rental::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('apartment_id')
                    ->label('Apartment')
                    ->options(Apartment::all()->mapWithKeys(fn (Apartment $apartment) => [$apartment->id => $apartment->__toString()]))
                    ->searchable(),
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
                Forms\Components\DatePicker::make('begins_at')
                    ->required(),
                Forms\Components\DatePicker::make('ends_at')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('apartment.name')->label(__('models.apartment.title')),
                Tables\Columns\TextColumn::make('begins_at')
                    ->date(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->date(),
                Tables\Columns\TextColumn::make('price_per_day')->money('EUR', true),
                Tables\Columns\TextColumn::make('price_total')->money('EUR', true),
            ])
            ->filters([
                SelectFilter::make('apartment_id')
                    ->options(Apartment::all()->mapWithKeys(fn (Apartment $apartment) => [$apartment->id => $apartment->__toString()])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->openUrlInNewTab(),
                Action::make('download')
                    ->url(fn (Rental $record): string => route('get.rentals.single.invoice', $record))
                    ->icon('heroicon-s-download')
                    ->color('secondary')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                BulkAction::make('download-bulk')
                    ->icon('heroicon-s-download')
                    ->color('secondary')
                    ->requiresConfirmation(false)
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $rentals) {
                        \Log::error('NUMBER OF RENTALS = '.$rentals->count());
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentals::route('/'),
            'create' => Pages\CreateRental::route('/create'),
            'edit' => Pages\EditRental::route('/{record}/edit'),
            'view' => Pages\ViewRental::route('/{record}'),
        ];
    }
}
