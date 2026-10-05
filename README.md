<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="art/logo-dark.svg">
    <img src="art/logo-light.svg" alt="Laravel Airlines" width="420">
  </picture>
</p>

[![Tests](https://github.com/FLAIRUK/laravel-airlines/actions/workflows/tests.yml/badge.svg)](https://github.com/FLAIRUK/laravel-airlines/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/ijeffro/laravel-airlines/v/stable)](https://packagist.org/packages/ijeffro/laravel-airlines)
[![License](https://poser.pugx.org/ijeffro/laravel-airlines/license)](https://packagist.org/packages/ijeffro/laravel-airlines)

IATA airline designators (`BA`, `EK`, `QF`, …) for Laravel 12 and 13.

- **No database required.** Look airlines up through a facade backed by an in-memory dataset.
- **Typed results.** Every lookup returns readonly `Airline` objects in Laravel collections.
- **Validation rule.** `new AirlineCode` accepts known designators only.
- **Optional table.** Publish a migration and seed an `airlines` table when other tables need to reference airlines.

## Installation

```bash
composer require ijeffro/laravel-airlines
```

Laravel discovers the service provider and the `Airlines` facade automatically.

## Usage

```php
use FLAIRUK\Airlines\Facades\Airlines;

Airlines::find('BA');            // Airline { id: 301, code: "BA", name: "British Airways", countryCode: "GB", countryName: "United Kingdom" }
Airlines::findOrFail('BA');      // throws ItemNotFoundException for unknown codes
Airlines::exists('ba');          // true (codes are case-insensitive)
Airlines::findById(301);

Airlines::all();                 // Collection<Airline>
Airlines::inCountry('GB');       // airlines registered in the UK
Airlines::search('british');     // matches on name or exact code
Airlines::codes();               // ['00', '01', ..., 'ZZ']
```

IATA re-issues designators, so a code can belong to more than one airline. `find()` returns the first one and `allWithCode('1I')` returns every match.

### Select options

```php
Airlines::options();             // [301 => 'British Airways', ...] keyed by id, sorted by name
Airlines::options('code');       // ['BA' => 'British Airways', ...]
```

### Validation

```php
use FLAIRUK\Airlines\Rules\AirlineCode;

$request->validate([
    'airline' => ['required', new AirlineCode],
]);
```

### Dependency injection

The facade resolves a singleton `FLAIRUK\Airlines\Airlines`, which you can type-hint instead:

```php
public function __construct(private \FLAIRUK\Airlines\Airlines $airlines) {}
```

## Database table (optional)

If you need airlines in your database, for example for foreign keys or joins:

```bash
php artisan airlines:install
```

This command publishes `config/airlines.php` and a migration, then offers to run `migrate` and seed the table. You can re-seed at any time; it upserts rows, so re-running is safe:

```bash
php artisan airlines:seed            # insert / update
php artisan airlines:seed --prune    # also delete rows no longer in the dataset
```

You can also call the seeder from your own `DatabaseSeeder`:

```php
$this->call(\FLAIRUK\Airlines\Database\AirlinesSeeder::class);
```

Query the table through the bundled Eloquent model:

```php
use FLAIRUK\Airlines\Models\Airline;

Airline::code('BA')->first();
Airline::inCountry('GB')->orderBy('name')->get();
```

The table name and connection come from `AIRLINES_TABLE` and `AIRLINES_DB_CONNECTION`, or from the published config.

## Upgrading from 1.x / dev-master

Version 2 is a rewrite. Breaking changes:

| 1.x | 2.x |
| --- | --- |
| `ijeffro\Airlines\…` namespace | `FLAIRUK\Airlines\…` |
| Facade `ijeffro\Airlines\AirlinesFacade` | `FLAIRUK\Airlines\Facades\Airlines` (auto-discovered) |
| `Airlines::getList($sort)` (array) | `Airlines::all()->sortBy($sort)` (Collection of `Airline`) |
| `Airlines::getOne($id)` | `Airlines::findById($id)` |
| `Airlines::getListForSelect()` | `Airlines::options()` |
| `php artisan airlines:migration` (generated seeder in `database/seeds`) | `php artisan airlines:install` / `airlines:seed` |
| Config key `airlines.table_name` | `airlines.table` |
| `charify` MySQL migration | removed. Columns are `CHAR` from the start |

Row `id`s are unchanged, so existing foreign keys stay valid. The dataset was also cleaned:

- 12 rows whose code was not a valid two-character designator (e.g. `&T`, `??`) were removed.
- 4 exact duplicate rows were removed.
- Missing country codes were filled in where the country was known.
- Empty strings are now `null`.

After upgrading, run `php artisan airlines:seed --prune` to update a seeded table.

## Testing

```bash
composer test
```

## License

MIT. See [LICENSE](LICENSE).
