<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\Enums\ApartmentState;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        $booking->apartment->update([
            'status' => ApartmentState::BOOKED,
        ]);
    }

    /**
     * Handle the Booking "updated" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function updated(Booking $booking)
    {
        //
    }

    public function deleted(Booking $booking): void
    {
        if ($booking->apartment->status === ApartmentState::RENOVATION->value ||
            $booking->apartment->status === ApartmentState::PREPARING->value) {
            return;
        }

        if ($booking->apartment->rents()->where('begins_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->exists()) {
            $booking->apartment->update([
                'status' => ApartmentState::BOOKED,
            ]);
        } else {
            $booking->apartment->update([
                'status' => ApartmentState::AVAILABLE,
            ]);
        }
    }

    /**
     * Handle the Booking "restored" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function restored(Booking $booking)
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     *
     * @param  \App\Models\Booking  $booking
     * @return void
     */
    public function forceDeleted(Booking $booking)
    {
        //
    }
}
