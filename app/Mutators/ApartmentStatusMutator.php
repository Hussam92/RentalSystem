<?php

namespace App\Mutators;

use App\Models\Apartment;
use App\Models\Enums\ApartmentState;

class ApartmentStatusMutator
{
    public static function available(Apartment $apartment): ApartmentState
    {
        return self::toggleState($apartment, ApartmentState::AVAILABLE);
    }

    public static function booked(Apartment $apartment): ApartmentState
    {
        return self::toggleState($apartment, ApartmentState::BOOKED);
    }

    public static function renovation(Apartment $apartment): ApartmentState
    {
        return self::toggleState($apartment, ApartmentState::RENOVATION);
    }

    public static function preparing(Apartment $apartment): ApartmentState
    {
        return self::toggleState($apartment, ApartmentState::PREPARING);
    }

    private static function toggleState(Apartment $apartment, ApartmentState $state): ApartmentState
    {
        $currentState = ApartmentState::fromName($apartment->status);

        if (! self::canToggle($currentState, $state)) {
            return $currentState;
        }

        $apartment->update(['status' => $state->value]);

        return $state;
    }

    public static function canToggle(ApartmentState $currentState, ApartmentState $finalState): bool
    {
        return collect($currentState->availableStates())->contains($finalState);
    }
}
