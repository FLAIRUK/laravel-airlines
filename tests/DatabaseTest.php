<?php

namespace FLAIRUK\Airlines\Tests;

use FLAIRUK\Airlines\Database\AirlinesSeeder;
use FLAIRUK\Airlines\Facades\Airlines;
use FLAIRUK\Airlines\Models\Airline;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class DatabaseTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    #[Test]
    public function the_seed_command_fills_the_table_and_is_idempotent(): void
    {
        $this->artisan('airlines:seed')->assertSuccessful();
        $this->artisan('airlines:seed')->assertSuccessful();

        $this->assertSame(Airlines::all()->count(), Airline::count());
        $this->assertSame('British Airways', Airline::code('ba')->first()->name);
        $this->assertTrue(Airline::inCountry('GB')->exists());
    }

    #[Test]
    public function the_seeder_can_be_called_from_an_application_seeder(): void
    {
        $this->seed(AirlinesSeeder::class);

        $this->assertSame(Airlines::all()->count(), Airline::count());
    }

    #[Test]
    public function prune_removes_rows_that_are_not_in_the_dataset(): void
    {
        Airline::create(['id' => 999999, 'code' => 'XX', 'name' => 'Defunct Air']);

        $this->artisan('airlines:seed', ['--prune' => true])->assertSuccessful();

        $this->assertNull(Airline::find(999999));
        $this->assertSame(Airlines::all()->count(), Airline::count());
    }

    #[Test]
    public function the_table_name_is_configurable(): void
    {
        config(['airlines.table' => 'iata_airlines']);
        (require __DIR__.'/../database/migrations/create_airlines_table.php')->up();

        $this->artisan('airlines:seed')->assertSuccessful();

        $this->assertTrue(Schema::hasTable('iata_airlines'));
        $this->assertSame(Airlines::all()->count(), Airline::count());
    }

    #[Test]
    public function install_publishes_the_config_and_a_timestamped_migration(): void
    {
        $migrations = database_path('migrations');
        File::delete(File::glob($migrations.'/*_create_airlines_table.php'));
        File::delete(config_path('airlines.php'));

        $this->artisan('airlines:install')
            ->expectsConfirmation('Run the migration and seed the airlines table now?', 'no')
            ->assertSuccessful();

        $this->assertFileExists(config_path('airlines.php'));
        $this->assertCount(1, File::glob($migrations.'/*_create_airlines_table.php'));

        File::delete(File::glob($migrations.'/*_create_airlines_table.php'));
        File::delete(config_path('airlines.php'));
    }
}
