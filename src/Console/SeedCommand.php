<?php

namespace FLAIRUK\Airlines\Console;

use FLAIRUK\Airlines\Airlines;
use FLAIRUK\Airlines\Database\AirlinesSeeder;
use FLAIRUK\Airlines\Models\Airline;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'airlines:seed')]
class SeedCommand extends Command
{
    protected $signature = 'airlines:seed
                            {--prune : Delete rows that are no longer in the dataset}';

    protected $description = 'Insert or update the airlines table from the bundled dataset';

    public function handle(Airlines $airlines): int
    {
        $this->laravel->call([$this->laravel->make(AirlinesSeeder::class), 'run']);

        if ($this->option('prune')) {
            $pruned = Airline::query()->whereNotIn('id', $airlines->all()->pluck('id'))->delete();
            $this->components->info("Pruned {$pruned} stale airlines.");
        }

        $this->components->info("Seeded {$airlines->all()->count()} airlines.");

        return self::SUCCESS;
    }
}
