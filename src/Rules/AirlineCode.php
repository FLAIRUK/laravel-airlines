<?php

namespace FLAIRUK\Airlines\Rules;

use Closure;
use FLAIRUK\Airlines\Airlines;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that the value is a known IATA airline designator.
 */
class AirlineCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! app(Airlines::class)->exists($value)) {
            $fail('The :attribute must be a valid IATA airline code.');
        }
    }
}
