<?php

namespace FLAIRUK\Airlines\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for the optional airlines table (see `php artisan airlines:install`).
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $country_code
 * @property string|null $country_name
 */
class Airline extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];

    public function getTable(): string
    {
        return config('airlines.table', 'airlines');
    }

    public function getConnectionName(): ?string
    {
        return $this->connection ?? config('airlines.connection');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeCode(Builder $query, string $code): void
    {
        $query->where('code', strtoupper($code));
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeInCountry(Builder $query, string $countryCode): void
    {
        $query->where('country_code', strtoupper($countryCode));
    }
}
