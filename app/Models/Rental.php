<?php

namespace App\Models;

use Database\Factories\RentalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Rental
 *
 * @property int $id
 * @property int|null $apartment_id
 * @property int $price
 * @property string $begins_at
 * @property string $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Apartment $apartment
 *
 * @method static \Database\Factories\RentalFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rental newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rental query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereApartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereBeginsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rental whereUpdatedAt($value)
 * @mixin \Eloquent
 *
 * @property int $price_per_day
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Rental wherePricePerDay($value)
 */
class Rental extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [];

    protected $casts = [
        'begins_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function apartment(): Model|BelongsTo
    {
        return $this->belongsTo(Apartment::class)->create();
    }

    protected static function newFactory(): RentalFactory
    {
        return RentalFactory::new();
    }
}
