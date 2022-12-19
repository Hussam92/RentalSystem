<?php

namespace App\Models;

use Database\Factories\ApartmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Apartment
 *
 * @property int $id
 * @property string $name
 * @property string $street
 * @property string $zip
 * @property int $bed_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rental[] $rents
 * @property-read int|null $rents_count
 *
 * @method static \Database\Factories\ApartmentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment whereBedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Apartment whereZip($value)
 * @mixin \Eloquent
 */
class Apartment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [];

    public function rents(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    protected static function newFactory(): ApartmentFactory
    {
        return ApartmentFactory::new();
    }

    public function __toString()
    {
        return $this->name.' - '.$this->street.'  ('.$this->bed_count.' P)';
    }
}
