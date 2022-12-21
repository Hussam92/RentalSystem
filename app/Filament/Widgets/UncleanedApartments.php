<?php

namespace App\Filament\Widgets;

use App\Models\Apartment;
use App\Models\Enums\ApartmentState;
use App\Mutators\ApartmentStatusMutator;
use Closure;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UncleanedApartments extends BaseWidget
{
    protected int|string|array $columnSpan = 12;

    protected function getTableQuery(): Builder
    {
        return Apartment::query()->where('status', ApartmentState::PREPARING->value)->orderBy('updated_at', 'asc');
    }

    protected function getTableColumns(): array
    {
        return [
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
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('available')
                ->label(Str::ucfirst(__('available')))
                ->color('success')
                ->icon('heroicon-o-badge-check')
                ->action(fn (Apartment $record): string => __(ApartmentStatusMutator::available($record)->value)),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (Apartment $record): string => route('filament.resources.apartments.view', ['record' => $record]);
    }
}
