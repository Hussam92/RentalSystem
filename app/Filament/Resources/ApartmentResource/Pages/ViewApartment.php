<?php

namespace App\Filament\Resources\ApartmentResource\Pages;

use App\Filament\Resources\ApartmentResource;
use App\Filament\Widgets\ApartmentSummaryWidget;
use App\Models\Apartment;
use App\Models\Enums\ApartmentState;
use App\Mutators\ApartmentStatusMutator;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Form;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

/**
 * @property Apartment $record
 */
class ViewApartment extends ViewRecord
{
    protected static string $resource = ApartmentResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('available')
                ->label(Str::ucfirst(__('available')))
                ->color('success')
                ->icon('heroicon-o-badge-check')
                ->visible(fn (): bool => ApartmentStatusMutator::canToggle(ApartmentState::fromName($this->record->status), ApartmentState::AVAILABLE))
                ->action(fn (): string => __(ApartmentStatusMutator::available($this->record)->value)),
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ApartmentSummaryWidget::class,
        ];
    }

    protected function form(Form $form): Form
    {
        return $form->schema([

        ]);
    }
}
