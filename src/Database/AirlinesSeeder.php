<?php

namespace FLAIRUK\Airlines\Database;

use FLAIRUK\Airlines\Airlines;
use FLAIRUK\Airlines\Data\Airline as AirlineData;
use FLAIRUK\Airlines\Models\Airline;
use Illuminate\Database\Seeder;

/**
 * Upserts the airline dataset into the airlines table. Safe to run repeatedly.
 */
class AirlinesSeeder extends Seeder
{
    public function run(Airlines $airlines): void
    {
        $airlines->all()
            ->map(fn (AirlineData $airline) => $airline->toArray())
            ->chunk(500)
            ->each(fn ($chunk) => Airline::query()->upsert(
                $chunk->values()->all(),
                ['id'],
                ['code', 'name', 'country_code', 'country_name'],
            ));
    }
}
