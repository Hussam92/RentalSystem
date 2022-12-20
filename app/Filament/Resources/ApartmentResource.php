<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApartmentResource\Pages;
use App\Models\Apartment;
use App\Models\Enums\ApartmentState;
use App\Mutators\ApartmentStatusMutator;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class ApartmentResource extends Resource
{
    protected static ?string $model = Apartment::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bed_count')
                    ->label('Anzahl Betten')
                    ->required(),
                Forms\Components\TextInput::make('street')
                    ->label('Strasse')
                    ->maxLength(255),
                Forms\Components\TextInput::make('zip')
                    ->label('PLZ')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('street')
                    ->label('Strasse'),
                Tables\Columns\TextColumn::make('zip')
                    ->label('PLZ'),
                Tables\Columns\TextColumn::make('bed_count')
                    ->label('Betten'),
                BadgeColumn::make('status')
                    ->translateLabel()
                    ->enum(
                        collect(array_column(ApartmentState::cases(), 'value'))
                            ->mapWithKeys(fn ($state) => [$state => __($state)])
                            ->toArray()
                    )
                    ->colors([
                        'primary',
                        'success' => ApartmentState::AVAILABLE->value,
                        'secondary' => ApartmentState::BOOKED->value,
                        'warning' => ApartmentState::PREPARING->value,
                        'danger' => ApartmentState::RENOVATION->value,
                    ])
                    ->icons([
                        'primary',
                        'heroicon-o-shield-check' => ApartmentState::AVAILABLE->value,
                        'heroicon-o-key' => ApartmentState::BOOKED->value,
                        'heroicon-o-hand' => ApartmentState::PREPARING->value,
                        'heroicon-o-exclamation' => ApartmentState::RENOVATION->value,
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(array_column(ApartmentState::cases(), 'value'))
                        ->mapWithKeys(fn ($state) => [$state => __($state)])
                        ->toArray())
                    ->translateLabel(),
            ])
            ->actions([
                Action::make('available')
                    ->label(__('available'))
                    ->color('success')
                    ->icon('heroicon-o-arrow-right')
                    ->visible(fn (Apartment $record): bool => ApartmentStatusMutator::canToggle(ApartmentState::fromName($record->status), ApartmentState::AVAILABLE))
                    ->action(fn (Apartment $record): string => __(ApartmentStatusMutator::available($record)->value)),
                Action::make('book')
                    ->label(__('book'))
                    ->color('primary')
                    ->icon('heroicon-o-arrow-right')
                    ->visible(fn (Apartment $record): bool => ApartmentStatusMutator::canToggle(ApartmentState::fromName($record->status), ApartmentState::BOOKED))
                    ->url(fn (Apartment $record) => BookingResource::getUrl('create'))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->color('secondary'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListApartments::route('/'),
            'create' => Pages\CreateApartment::route('/create'),
            'edit' => Pages\EditApartment::route('/{record}/edit'),
            'view' => Pages\ViewApartment::route('/{record}'),
        ];
    }

    public static function getTranslatableLocales(): array
    {
        return ['de', 'en', 'es'];
    }
}
