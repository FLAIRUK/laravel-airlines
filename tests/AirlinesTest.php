<?php

namespace FLAIRUK\Airlines\Tests;

use FLAIRUK\Airlines\Airlines as AirlinesRepository;
use FLAIRUK\Airlines\Data\Airline;
use FLAIRUK\Airlines\Facades\Airlines;
use FLAIRUK\Airlines\Rules\AirlineCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ItemNotFoundException;
use PHPUnit\Framework\Attributes\Test;

class AirlinesTest extends TestCase
{
    #[Test]
    public function it_resolves_a_singleton_through_the_facade_and_alias(): void
    {
        $this->assertSame(app(AirlinesRepository::class), app('airlines'));
        $this->assertInstanceOf(AirlinesRepository::class, Airlines::getFacadeRoot());
    }

    #[Test]
    public function the_dataset_is_well_formed(): void
    {
        $airlines = Airlines::all();

        $this->assertGreaterThan(1000, $airlines->count());
        $this->assertContainsOnlyInstancesOf(Airline::class, $airlines);
        $this->assertSame($airlines->count(), $airlines->pluck('id')->unique()->count(), 'ids must be unique');

        $airlines->each(function (Airline $airline) {
            $this->assertMatchesRegularExpression('/^[A-Z0-9]{2}$/', $airline->code);
            $this->assertNotSame('', $airline->name);
            $this->assertTrue($airline->countryCode === null || preg_match('/^[A-Z]{2}$/', $airline->countryCode) === 1);
        });
    }

    #[Test]
    public function it_finds_airlines_by_code_case_insensitively(): void
    {
        $airline = Airlines::find(' ba ');

        $this->assertSame('BA', $airline->code);
        $this->assertSame('British Airways', $airline->name);
        $this->assertSame('GB', $airline->countryCode);
        $this->assertNull(Airlines::find('$$'));
        $this->assertTrue(Airlines::exists('ba'));
        $this->assertFalse(Airlines::exists('$$'));
    }

    #[Test]
    public function find_or_fail_throws_for_unknown_codes(): void
    {
        $this->expectException(ItemNotFoundException::class);

        Airlines::findOrFail('$$');
    }

    #[Test]
    public function it_returns_every_airline_sharing_a_reissued_code(): void
    {
        $code = collect(array_count_values(Airlines::all()->pluck('code')->all()))
            ->filter(fn (int $count) => $count > 1)
            ->keys()
            ->first();

        $this->assertNotNull($code);
        $this->assertGreaterThan(1, Airlines::allWithCode($code)->count());
        $this->assertSame(Airlines::allWithCode($code)->first(), Airlines::find($code));
    }

    #[Test]
    public function it_finds_by_id(): void
    {
        $first = Airlines::all()->first();

        $this->assertSame($first, Airlines::findById($first->id));
        $this->assertNull(Airlines::findById(-1));
    }

    #[Test]
    public function it_filters_by_country(): void
    {
        $british = Airlines::inCountry('gb');

        $this->assertNotEmpty($british);
        $this->assertTrue($british->every(fn (Airline $a) => $a->countryCode === 'GB'));
        $this->assertSame(range(0, $british->count() - 1), $british->keys()->all());
    }

    #[Test]
    public function it_searches_by_name_and_ranks_exact_code_matches_first(): void
    {
        $this->assertTrue(Airlines::search('british')->contains('code', 'BA'));
        $this->assertSame('BA', Airlines::search('ba')->first()->code);
        $this->assertCount(0, Airlines::search('  '));
    }

    #[Test]
    public function it_builds_select_options(): void
    {
        $options = Airlines::options();
        $byCode = Airlines::options('code');

        $this->assertSame(Airlines::all()->count(), $options->count());
        $this->assertSame('British Airways', $byCode['BA']);
        $this->assertSame($options->values()->sort(SORT_NATURAL | SORT_FLAG_CASE)->values()->all(), $options->values()->all());
    }

    #[Test]
    public function data_objects_serialise_to_snake_case_arrays(): void
    {
        $this->assertSame(
            ['code' => 'BA', 'name' => 'British Airways', 'country_code' => 'GB', 'country_name' => 'United Kingdom'],
            collect(Airlines::find('BA')->toArray())->except('id')->all(),
        );
        $this->assertJson(json_encode(Airlines::find('BA')));
    }

    #[Test]
    public function the_validation_rule_accepts_known_codes_only(): void
    {
        $this->assertTrue(Validator::make(['airline' => 'ba'], ['airline' => new AirlineCode])->passes());
        $this->assertFalse(Validator::make(['airline' => 'ZZZ'], ['airline' => new AirlineCode])->passes());
        $this->assertFalse(Validator::make(['airline' => ['BA']], ['airline' => new AirlineCode])->passes());
    }

    #[Test]
    public function codes_are_always_strings(): void
    {
        $codes = Airlines::codes();

        $this->assertContains('00', $codes);
        $this->assertSame([], array_filter($codes, fn ($code) => ! is_string($code)));
    }
}
