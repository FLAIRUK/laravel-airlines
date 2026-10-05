<?php

namespace FLAIRUK\Airlines\Data;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @implements Arrayable<string, int|string|null>
 */
final readonly class Airline implements Arrayable, JsonSerializable
{
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public ?string $countryCode,
        public ?string $countryName,
    ) {}

    /**
     * @param  array{id: int, code: string, name: string, country_code: ?string, country_name: ?string}  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            id: $row['id'],
            code: $row['code'],
            name: $row['name'],
            countryCode: $row['country_code'],
            countryName: $row['country_name'],
        );
    }

    /**
     * @return array{id: int, code: string, name: string, country_code: ?string, country_name: ?string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'country_code' => $this->countryCode,
            'country_name' => $this->countryName,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
