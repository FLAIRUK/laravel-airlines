<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="art/logo-dark.svg">
    <img src="art/logo-light.svg" alt="Laravel Airlines" width="420">
  </picture>
</p>

<h2 align="center">
  <a href="https://www.php.net/" target="_blank"><img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white" alt="PHP 8.2+"></a>&nbsp;
  <a href="https://laravel.com/docs/" target="_blank"><img src="https://img.shields.io/badge/Laravel-12%20%7C%2013-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel 12 or 13"></a>&nbsp;
  <a href="https://github.com/FLAIRUK/laravel-airlines/actions/workflows/tests.yml" target="_blank"><img src="https://img.shields.io/badge/Lint-%E2%9C%93-2EA043?style=flat&logo=githubactions&logoColor=white" alt="Lint"></a>&nbsp;
  <a href="https://github.com/FLAIRUK/laravel-airlines/actions/workflows/tests.yml" target="_blank"><img src="https://img.shields.io/badge/Tests-%E2%9C%93-2EA043?style=flat&logo=githubactions&logoColor=white" alt="Tests"></a>&nbsp;
  <a href="https://packagist.org/packages/ijeffro/laravel-airlines" target="_blank"><img src="https://img.shields.io/packagist/dt/ijeffro/laravel-airlines?style=flat&logo=packagist&logoColor=white&label=Downloads&color=F28D1A" alt="Downloads on Packagist"></a>&nbsp;
  <a href="https://github.com/FLAIRUK/laravel-airlines/blob/master/LICENSE" target="_blank"><img src="https://img.shields.io/github/license/FLAIRUK/laravel-airlines?style=flat&label=License&color=3DA639" alt="MIT licence"></a>&nbsp;
  <a href="https://www.iata.org/en/publications/directories/code-search/" target="_blank"><img src="https://img.shields.io/badge/Data-IATA-2563EB?style=flat" alt="IATA"></a>&nbsp;
  <br>&nbsp;
</h2>

**Laravel Airlines** — IATA airline designators (`BA`, `EK`, `QF`, …) for Laravel 12 and 13.

- **No database required.** Look airlines up through a facade backed by an in-memory dataset.
- **Typed results.** Every lookup returns readonly `Airline` objects in Laravel collections.
- **Validation rule.** `new AirlineCode` accepts known designators only.
- **Optional table.** Publish a migration and seed an `airlines` table when other tables need to reference airlines.

<p align="center">
  📦&nbsp;<a href="#-installation">Installation</a> ·
  🚀&nbsp;<a href="#-usage">Usage</a> ·
  💾&nbsp;<a href="#-database-table-optional">Database table</a> ·
  🔄&nbsp;<a href="#-upgrading-from-dev-master">Upgrading</a>
</p>

<br><br>

## 📦 Installation

```bash
composer require ijeffro/laravel-airlines
```

Laravel discovers the service provider and the `Airlines` facade automatically.

<br><br>

## 🚀 Usage

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

<br><br>

## 💾 Database table (optional)

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

<br><br>

## 🔄 Upgrading from dev-master

Version 1.0 is a rewrite. Breaking changes:

| dev-master | 1.0 |
| --- | --- |
| `ijeffro\Airlines\…` namespace | `FLAIRUK\Airlines\…` |
| Facade `ijeffro\Airlines\AirlinesFacade` | `FLAIRUK\Airlines\Facades\Airlines` (auto-discovered) |
| `Airlines::getList($sort)` (array) | `Airlines::all()->sortBy($sort)` (Collection of `Airline`) |
| `Airlines::getOne($id)` | `Airlines::findById($id)` |
| `Airlines::getListForSelect()` | `Airlines::options()` |
| `php artisan airlines:migration` (generated seeder in `database/seeds`) | `php artisan airlines:install` / `airlines:seed` |
| Config key `airlines.table_name` | `airlines.table` |
| `charify` MySQL migration | removed. `code` and `country_code` are `CHAR` from the start |

Row `id`s are unchanged, so existing foreign keys stay valid. The dataset was also cleaned:

- 12 rows whose code was not a valid two-character designator (e.g. `&T`, `??`) were removed.
- 4 exact duplicate rows were removed.
- Missing country codes were filled in where the country was known.
- Empty strings are now `null`.

After upgrading, run `php artisan airlines:seed --prune` to update a seeded table.

<br><br>

## 🧪 Testing

```bash
composer test
```

<br><br>

## 📄 License

MIT. See [LICENSE](LICENSE).
