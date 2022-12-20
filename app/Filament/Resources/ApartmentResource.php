<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApartmentResource\Pages;
use App\Models\Apartment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

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
                    ->label('Anzahl Betten'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
