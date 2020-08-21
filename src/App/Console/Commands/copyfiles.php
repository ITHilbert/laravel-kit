<?php

namespace ITHilbert\LaravelKit\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class copyfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelkit:copyfiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kopiert die Laravelkit Datein ins Project';

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
        //Laravel UI
        $this->info('Dateien werden kopiert');

        //LaravelKit Dateien kopieren
        exec('php artisan vendor:publish --provider="ITHilbert\LaravelKit\LaravelKitServiceProvider" --force');

        return 0;
    }
}
