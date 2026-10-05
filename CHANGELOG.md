# Changelog

## 1.0.1 - 2026-10-05

- `codes()` returns every designator as a string; all-digit ones such as `00` came back as integers.
- README: requirements, `airlines:install --migrate`, the camelCase `sortBy` mapping, and a warning that old `NOT NULL` country columns must be made nullable before seeding.

## 1.0.0 - 2026-10-05

Complete rewrite for Laravel 12 and 13 (PHP 8.2+). See the upgrade guide in the README.

- In-memory lookup API (`find`, `findOrFail`, `exists`, `inCountry`, `search`, `options`, …) returning readonly `Airline` objects.
- `AirlineCode` validation rule.
- Package auto-discovery; `FLAIRUK\Airlines` namespace.
- Optional publishable migration, Eloquent model, idempotent seeder, `airlines:install` and `airlines:seed` commands.
- Dataset cleaned: invalid and duplicate rows removed, missing country codes filled, empty strings now `null`.
- Test suite and GitHub Actions CI.
