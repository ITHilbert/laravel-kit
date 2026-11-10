<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;

class LaravelKitInstalliSeed extends Command
{
    protected $signature = 'install:iSeed';

    protected $description = 'Installiert ISeed (als Dev-Abhängigkeit)';

    public function handle(): int
    {
        $this->info('Install orangehill/iseed (--dev)');

        $kommandozeile = 'composer require --dev orangehill/iseed';

        if (app()->bound('laravelkit.exec')) {
            /** @var callable(string): int $ausfuehren */
            $ausfuehren = app('laravelkit.exec');

            return (int) $ausfuehren($kommandozeile);
        }

        passthru($kommandozeile, $exitCode);

        return $exitCode;
    }
}
