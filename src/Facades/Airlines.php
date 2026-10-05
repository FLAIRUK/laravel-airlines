<?php

namespace FLAIRUK\Airlines\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection<int, \FLAIRUK\Airlines\Data\Airline> all()
 * @method static \FLAIRUK\Airlines\Data\Airline|null find(string $code)
 * @method static \FLAIRUK\Airlines\Data\Airline findOrFail(string $code)
 * @method static \FLAIRUK\Airlines\Data\Airline|null findById(int $id)
 * @method static \Illuminate\Support\Collection<int, \FLAIRUK\Airlines\Data\Airline> allWithCode(string $code)
 * @method static bool exists(string $code)
 * @method static \Illuminate\Support\Collection<int, \FLAIRUK\Airlines\Data\Airline> inCountry(string $countryCode)
 * @method static \Illuminate\Support\Collection<int, \FLAIRUK\Airlines\Data\Airline> search(string $term)
 * @method static \Illuminate\Support\Collection<int|string, string> options(string $key = 'id', string $label = 'name')
 * @method static list<string> codes()
 *
 * @see \FLAIRUK\Airlines\Airlines
 */
class Airlines extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \FLAIRUK\Airlines\Airlines::class;
    }
}
