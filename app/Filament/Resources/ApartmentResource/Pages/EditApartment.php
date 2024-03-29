<?php

namespace App\Filament\Resources\ApartmentResource\Pages;

use App\Filament\Resources\ApartmentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApartment extends EditRecord
{
    protected static string $resource = ApartmentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make()->color('success'),
            Actions\DeleteAction::make(),
        ];
    }
}
