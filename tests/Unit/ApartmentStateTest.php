<?php

namespace Tests\Unit;

use App\Models\Apartment;
use App\Models\Enums\ApartmentState;
use App\Mutators\ApartmentStatusMutator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApartmentStateTest extends TestCase
{
    use RefreshDatabase;

    public function testToggleApartmentState()
    {
        $apartment = Apartment::factory()->create();

        self::assertEquals(ApartmentState::AVAILABLE->value, $apartment->status);

        ApartmentStatusMutator::booked($apartment);

        self::assertEquals(ApartmentState::BOOKED->value, $apartment->status);

        ApartmentStatusMutator::available($apartment);

        self::assertEquals(ApartmentState::BOOKED->value, $apartment->status);

        ApartmentStatusMutator::preparing($apartment);

        self::assertEquals(ApartmentState::PREPARING->value, $apartment->status);

        ApartmentStatusMutator::available($apartment);

        self::assertEquals(ApartmentState::AVAILABLE->value, $apartment->status);

        ApartmentStatusMutator::renovation($apartment);

        self::assertEquals(ApartmentState::RENOVATION->value, $apartment->status);

        ApartmentStatusMutator::booked($apartment);

        self::assertEquals(ApartmentState::RENOVATION->value, $apartment->status);
    }
}
