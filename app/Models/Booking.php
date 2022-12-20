<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Booking
 *
 * @property int $id
 * @property int|null $apartment_id
 * @property float $price_per_day
 * @property float $price_total
 * @property Carbon $begins_at
 * @property Carbon $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Apartment $apartment
 *
 * @method static \Database\Factories\BookingFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereApartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBeginsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Booking wherePricePerDay($value)
 */
class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [];

    protected $casts = [
        'begins_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function daysCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->begins_at->diffInDays($this->ends_at),
        );
    }

    public function priceTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price_per_day * $this->daysCount,
        );
    }

    public function apartment(): Model|BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    protected static function newFactory(): BookingFactory
    {
        return BookingFactory::new();
    }
}
