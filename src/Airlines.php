<?php

namespace FLAIRUK\Airlines;

use FLAIRUK\Airlines\Data\Airline;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;

/**
 * In-memory lookup of IATA airline designators.
 *
 * The dataset is loaded lazily on first use and kept for the lifetime of the
 * instance (bound as a singleton), so lookups never touch the database.
 */
class Airlines
{
    /** @var Collection<int, Airline>|null */
    protected ?Collection $airlines = null;

    /** @var array<string, list<Airline>>|null */
    protected ?array $byCode = null;

    public function __construct(
        protected string $path = __DIR__.'/../data/airlines.php',
    ) {}

    /**
     * Every airline, in dataset order.
     *
     * @return Collection<int, Airline>
     */
    public function all(): Collection
    {
        return $this->airlines ??= collect(require $this->path)
            ->map(fn (array $row) => Airline::fromArray($row))
            ->values();
    }

    /**
     * The first airline using the given IATA designator, e.g. "BA".
     *
     * IATA re-issues designators, so a code can map to more than one airline;
     * use {@see self::allWithCode()} to get every match.
     */
    public function find(string $code): ?Airline
    {
        return $this->allWithCode($code)->first();
    }

    /**
     * @throws ItemNotFoundException
     */
    public function findOrFail(string $code): Airline
    {
        return $this->find($code) ?? throw new ItemNotFoundException("Unknown airline code [{$code}].");
    }

    public function findById(int $id): ?Airline
    {
        return $this->all()->firstWhere('id', $id);
    }

    /**
     * @return Collection<int, Airline>
     */
    public function allWithCode(string $code): Collection
    {
        return collect($this->index()[strtoupper(trim($code))] ?? []);
    }

    public function exists(string $code): bool
    {
        return isset($this->index()[strtoupper(trim($code))]);
    }

    /**
     * Airlines registered in the given ISO 3166-1 alpha-2 country, e.g. "GB".
     *
     * @return Collection<int, Airline>
     */
    public function inCountry(string $countryCode): Collection
    {
        return $this->all()->where('countryCode', strtoupper($countryCode))->values();
    }

    /**
     * Case-insensitive match against the code or name.
     *
     * @return Collection<int, Airline>
     */
    public function search(string $term): Collection
    {
        $term = trim($term);

        if ($term === '') {
            return new Collection;
        }

        return $this->all()
            ->filter(fn (Airline $airline) => strcasecmp($airline->code, $term) === 0
                || mb_stripos($airline->name, $term) !== false)
            ->sortBy(fn (Airline $airline) => strcasecmp($airline->code, $term) === 0 ? 0 : 1)
            ->values();
    }

    /**
     * Key/label pairs for a <select>, sorted by label.
     *
     * Keyed by id by default because designators are not unique.
     *
     * @return Collection<int|string, string>
     */
    public function options(string $key = 'id', string $label = 'name'): Collection
    {
        return $this->all()->sortBy($label, SORT_NATURAL | SORT_FLAG_CASE)->pluck($label, $key);
    }

    /**
     * @return list<string>
     */
    public function codes(): array
    {
        // All-digit designators such as "00" become int array keys; hand them back as strings.
        return array_map(strval(...), array_keys($this->index()));
    }

    /**
     * @return array<string, list<Airline>>
     */
    protected function index(): array
    {
        if ($this->byCode === null) {
            $this->byCode = [];

            foreach ($this->all() as $airline) {
                $this->byCode[$airline->code][] = $airline;
            }
        }

        return $this->byCode;
    }
}
