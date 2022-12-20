<?php

namespace App\Models\Enums;

enum ApartmentState: string
{
    case AVAILABLE = 'available';
    case BOOKED = 'booked';
    case PREPARING = 'preparing';
    case RENOVATION = 'renovation';

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::BOOKED => 'primary',
            self::PREPARING => 'danger',
            self::RENOVATION => 'warning',
        };
    }

    public static function fromName(string $name): self
    {
        foreach (self::cases() as $status) {
            if ($name === $status->value) {
                return $status;
            }
        }

        throw new \ValueError("$name is not a valid backing value for enum ".self::class);
    }

    public function availableStates(): array
    {
        return match ($this) {
            self::AVAILABLE => [
                self::BOOKED,
                self::RENOVATION,
            ],
            self::BOOKED => [
                self::PREPARING,
            ],
            self::PREPARING => [
                self::AVAILABLE,
                self::RENOVATION,
            ],
            self::RENOVATION => [
                self::AVAILABLE,
            ],
        };
    }
}
