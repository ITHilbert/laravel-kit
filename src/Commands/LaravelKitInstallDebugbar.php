<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallDebugbar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:Debugbar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert Debugbar';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Debugbar
        $this->info('Install barryvdh/laravel-debugbar');
        exec('composer require barryvdh/laravel-debugbar --dev');
        exec('php artisan vendor:publish --provider="Barryvdh\\Debugbar\\ServiceProvider"');

        return 0;
    }
}
